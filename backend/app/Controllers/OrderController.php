<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\WalletModel;
use App\Models\WalletTransactionModel;
use App\Models\OrderQueueModel;
use CodeIgniter\RESTful\ResourceController;

class OrderController extends ResourceController
{
    public function create()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $data = $this->request->getJSON(true);

        $walletModel = new WalletModel();
        $productModel = new ProductModel();
        $orderModel = new OrderModel();
        $orderItemModel = new OrderItemModel();
        $walletTransactionModel = new WalletTransactionModel();
        $queueModel = new OrderQueueModel();

        // Get user's wallet
        $wallet = $walletModel
            ->where('user_id', $data['user_id'])
            ->first();

        if (!$wallet) {
            return $this->failNotFound('Wallet not found');
        }

        // Get product
        $product = $productModel->find($data['product_id']);

        if (!$product) {
            return $this->failNotFound('Product not found');
        }

        if ($product['stock'] < $data['quantity']) {
            return $this->fail('Insufficient stock');
        }

        $total = $product['price'] * $data['quantity'];

        if ($wallet['balance'] < $total) {
            return $this->fail('Insufficient wallet balance');
        }

        // Deduct wallet
        $walletModel->update($wallet['id'], [
            'balance' => $wallet['balance'] - $total
        ]);

        // Wallet transaction
        $walletTransactionModel->insert([
            'wallet_id' => $wallet['id'],
            'type' => 'debit',
            'amount' => $total,
            'description' => 'Order payment'
        ]);

        // Create order
        $orderId = $orderModel->insert([
            'user_id' => $data['user_id'],
            'status' => 'queued',
            'total' => $total
        ]);

        // Order item
        $orderItemModel->insert([
            'order_id' => $orderId,
            'product_id' => $product['id'],
            'quantity' => $data['quantity'],
            'price' => $product['price']
        ]);

        // Reduce stock
        $productModel->update($product['id'], [
            'stock' => $product['stock'] - $data['quantity']
        ]);

        // Queue order
        $queueModel->insert([
            'order_id' => $orderId,
            'status' => 'pending',
            'attempts' => 0,
            'available_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->failServerError('Failed to place order');
        }

        return $this->respondCreated([
            'message' => 'Order queued successfully',
            'order_id' => $orderId
        ]);
    }
}
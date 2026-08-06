<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\RESTful\ResourceController;

class ProductController extends ResourceController
{
    public function index()
    {
        return $this->respond(
            (new ProductModel())->findAll()
        );
    }

    public function create()
{
    $model = new ProductModel();

    $data = $this->request->getJSON(true);

    if (
        empty($data['name']) ||
        empty($data['price']) ||
        empty($data['stock'])
    ) {
        return $this->failValidationErrors('Name, price and stock are required.');
    }

    $id = $model->insert($data);

    return $this->respondCreated([
        'message' => 'Product created successfully',
        'id' => $id
    ]);
}
}
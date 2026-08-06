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

        $id = $model->insert($data);

        return $this->respondCreated([
            'message' => 'Product created',
            'id' => $id
        ]);
    }
}
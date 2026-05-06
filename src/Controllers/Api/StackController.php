<?php

namespace App\Controllers\Api;

use App\Models\Api\StackModel;

class StackController
{

    public function getStacks() {
        $model = new StackModel();
        $stacks = $model->findAll();

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode([
            "status" => "success",
            "count" => count($stacks),
            "data" => $stacks
        ]);

        exit;
    }

}
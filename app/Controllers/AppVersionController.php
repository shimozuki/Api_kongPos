<?php

namespace App\Controllers;

use App\Models\PesananModel;
use CodeIgniter\RESTful\ResourceController;
use Exception;
use PhpParser\Node\Stmt\Echo_;


class AppVersionController extends ResourceController
{
    public function version()
    {
        $model = new PesananModel();

        $query = $model->query("SELECT * FROM g_app_version WHERE jenis = 1 ");

        if(!empty($query))
        {
            $respond = [
                "version" => $query->getRow('app_store_version'),
                "status" => $query->getRow('version_level'),
              ];
              return $this->respondCreated($respond);
        }else{
            $respond = [
                'status' => 200,
                'error' => false,
                'messages' => "Sorry Not Found ",
                'data' => '0'
            ];
            return $this->respondCreated($respond);
        }
    }
}

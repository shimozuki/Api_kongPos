<?php

namespace App\Controllers;

use App\Models\PesananModel;
use CodeIgniter\RESTful\ResourceController;
use Exception;

class Pesanan extends ResourceController
{
    public function transaksi()
    {
        date_default_timezone_set("Asia/Jakarta");
        $companyid = $this->request->getVar('company_id');
        $startdate = date('Y-m-d', strtotime("-2 day", strtotime(date("Y-m-d"))));
        $endate = date("Y-m-d");

        if(empty($companyid))
        {
            $respond = [
                'status' => 500,
                'error' => true,
                'messages' => "company id kosong",
                'data' => []
            ];
            return $this->respondCreated($respond);
        }else{
            $model = new PesananModel();

            $query = $model->query("SELECT t_penjualan.no_transaksi, t_pengiriman.no_penjualan, t_pengiriman.nama_tujuan, 
            t_driver.kode_pin, m_user_company.company_id, m_driver.nama_depan, m_driver.hp1, t_penjualan.id
            FROM t_pengiriman 
            INNER JOIN t_penjualan ON t_pengiriman.no_resi= t_penjualan.no_transaksi
            INNER JOIN t_driver ON t_pengiriman.id_driver = t_driver.kd_driver
            INNER JOIN m_driver ON t_driver.kd_driver = m_driver.kd_driver
            INNER JOIN m_user_company ON t_penjualan.user_id_toko = m_user_company.id
            WHERE m_user_company.company_id = '$companyid' AND date(t_penjualan.tanggal) BETWEEN '$startdate' AND '$endate'
            AND t_penjualan.status_barang = 4 ")->getRow();

            if (empty($query)) {
                $respond = [
                    'status' => 200,
                    'error' => false,
                    'messages' => "Sorry Not Found ",
                    'data' => []
                ];
                return $this->respondCreated($respond);
            } else {
                $no_transaksi = $query->no_transaksi;

                if (empty($no_transaksi)) {
                    $respond = [
                        'status' => 500,
                        'error' => true,
                        'messages' => "Sorry Not Found ",
                        'data' => []
                    ];
                    return $this->respondCreated($respond);
                } else {
                    $pesanan = $model->query("SELECT t_penjualan_detail.*, m_barang.kd_barang, m_satuan.kd_satuan
                    FROM t_penjualan_detail 
                    INNER JOIN m_barang_satuan ON t_penjualan_detail.item_id = m_barang_satuan.id
                    INNER JOIN m_barang ON m_barang_satuan.barang_id = m_barang.id
                    INNER JOIN m_satuan ON m_barang_satuan.satuan_id = m_satuan.id
                    WHERE t_penjualan_detail.no_transaksi = '$no_transaksi'");

                    $respond = [
                        'data' => [
                           "no_transaksi" => $no_transaksi,
                           "pembeli"      => $query->nama_tujuan,
                           "pesanan"      => ($pesanan->getResult()),
                           "id_order"     => $query->id,
                           "pin"          => $query->kode_pin,
                           "noHp"         => $query->hp1,
                           "comp_id"      => $query->company_id,
                           "nama_driver"  => $query->nama_depan,
                        ]
                    ];
                return $this->respondCreated($respond);
              
                }
            }
        }
    }
}

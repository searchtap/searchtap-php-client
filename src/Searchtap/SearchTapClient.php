<?php

namespace Searchtap;

class SearchTapClient
{
    private $collectionName;
    private $adminKey;
    private $cert_path;
    public $page;
    public $offset;
    public $facet = array();
    public $filter;
    public $sort = array();
    public $fields = array();
    public $typoTolerance;
    public $searchFields = array();

    public function __construct($collectionName, $adminKey)
    {
        $this->collectionName = $collectionName;
        $this->adminKey = $adminKey;
        $this->cert_path = dirname(__FILE__).'/../../gs_cert/searchtap.io.crt';
        $this->page = 10;
        $this->offset = 0;
    }

    public function setCollection($collectionName){
        $this->collectionName = $collectionName;
        return $this;
    }

    public function page($pageSize){
        $this->page = $pageSize;
        return $this;
    }

    public function offset($offset){
        $this->offset = $offset;
        return $this;
    }

    public function fields($fields){
        $this->fields = $fields;
        return $this;
    }

    public function sort($item){
        $this->sort = $item;
        return $this;
    }

    public function facets($facets_items){
        $this->facet= $facets_items;
        return $this;
    }

    public function search_fields($searchFields){
        $this->searchFields = $searchFields;
        return $this;
    }

    public function typoTolerance(){
        $this->typoTolerance = 1;
        return $this;
    }

    public function search($searchQuery){

        $query = array("query" => $searchQuery,
                        "fields" => $this->fields,
                        "facets" => $this->facet,
                        "searchFields" => $this->searchFields,
                        "pageSize" => $this->page,
                        "offset" => $this->offset,
                        "sort" => $this->sort,
                        "typoTolerance" => $this->typoTolerance
                    );

        $query_json = json_encode($query);
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.searchtap.io/v1/collections/" . $this->collectionName . "/query",
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => $this->cert_path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $query_json,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json",
                "x-auth-token: " . $this->adminKey
            ),
        ));

        $result = curl_exec($curl);
        return $result;
    }

    public function index($productArray)
    {
        $product_json = json_encode($productArray);

        if($product_json) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.searchtap.io/v1/collections/" . $this->collectionName,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_CAINFO => $this->cert_path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $product_json,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                    "x-auth-token: " . $this->adminKey
                ),
            ));

            curl_exec($curl);
            $response = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);
            if($err)
                echo $err;

            curl_close($curl);

            return $response;
        }
    }

    public function remove($productIds)
    {
        $curl = curl_init();

        $data_json = json_encode($productIds);

        if($data_json) {
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.searchtap.io/v1/collections/" . $this->collectionName . "/delete",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_CAINFO => $this->cert_path,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_POSTFIELDS => $data_json,
                CURLOPT_HTTPHEADER => array(
                    "cache-control: no-cache",
                    "content-type: application/json",
                    "x-auth-token: " . $this->adminKey
                ),
            ));

            curl_exec($curl);
            $response = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);
            if ($err)
                echo $err;

            curl_close($curl);

            return $response;
        }
    }
}
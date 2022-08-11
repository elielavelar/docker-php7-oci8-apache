<?php

namespace common\components\HttpClient;

interface HttpInterface
{
    public function find();
    public function get();
    public function post();
    public function put();
    public function options();
    public function delete();
    public function patch();
    public function head();
}
<?php

class ModulesCache {
    public $key = '';
    public $data = null;

    function __construct($page, $field, $raw){
        $this->key = 'modules::' . $page . '::' . $field;
        $data = s::get($this->key);

        if(!isset($data)){
            $data = (array)yaml::decode($raw);
        }

        $this->update($data);
    }

    public function collection($path = array()){
        $data = $this->get($path);
        $coll = new Collection($data);

        $coll = $coll->map(function($item) {
            return new Obj($item);
        });

        return $coll;
    }

    public function data(){
        return $this->data;
    }

    // TODO: Should be $path, $data ?
    function update($data){
        $arr = array();
        foreach($data as $k => $v){
            if(!isset($v['id'])){
                $v['id'] = str::random(32);
            }
            $arr[$v['id']] = $v;
        }

        $this->data = $arr;
        $this->save();
    }

    function save(){
        s::set($this->key, $this->data);
    }

    function add($path, $data){
        array_shift($path);
        $node = &$this->data;

        foreach($path as $part){
            if(!isset($node[$part])){
                $node[$part] = array();
            }
            $node = &$node[$part];
        }

        if(!isset($data['id'])){
            $data['id'] = str::random(32);
        }

        $node[$data['id']] = $data;

        $this->save();
    }

    function parent($path) {
        if(count($path) > 1) array_pop($path);
        return $this->get($path);
    }

    function ids(&$arr){
        foreach($arr as $k => $v){
            if(!isset($v['id'])){
                $id = str::random(32);
                $v['id'] = $id;
            }
            $arr[$v['id']] = $v;
        }

        return $arr;
    }

    function updateIn($path, $data){
        $node = &$this->data;
        foreach($path as $part){
            if(!isset($node[$part])) $node[$part] = array();

            $node = &$node[$part];
        }

        // foreach(&$this->data as $v)

        $node = $data;
        $this->save();
    }

    function get($path) {
        print_r($path);
        array_shift($path);
        $ret = $this->data;

        foreach($path as $part) {
            $ret = isset($ret[$part]) ? $ret[$part] : array();
        }

        if(is_array($ret)) {
            $ret = $this->ids($ret);
            $this->updateIn($path, $ret);
        }

        print_r($this->data);
        return $ret;
    }

    function remove($id){
    }
}


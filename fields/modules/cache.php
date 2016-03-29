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

    function save(){
        s::set($this->key, $this->data);
    }

    public function data(){
        return $this->data;
    }

    public function collection($path = array()){
        $data = $this->get($path);
        $coll = new Collection($data);

        $coll = $coll->map(function($item) {
            return new Obj($item);
        });

        return $coll;
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

    function add($path, $data){
        array_shift($path);
        $node = $this->data;

        foreach($path as $p) {
            $node = isset($node[$p]) ? $node[$p] : array();
        }

        if(is_array($node) && !$this->assoc($node)) {
            $node = $this->withIds($node);
        }

        if(!isset($node['id'])) {
            $data['id'] = str::random(32);
            $node[$data['id']] = $data;
        }

        $this->updateIn($path, $node);
        return $node;
    }

    function parent($path) {
        if(count($path) > 1) array_pop($path);
        return $this->get($path);
    }

    function updateIn($path, $data){
        return $data;

        $last = end($path);
        reset($path);

        $node = &$this->data;
        foreach($path as $part){
            if(!isset($node[$part])) return array();

            if($part === $last){
                $node[$part] = $data;
            } else {
                $node = &$node[$part];
            }
        }

        $this->save();
    }

    function get($path) {
        array_shift($path);
        $node = $this->data;

        foreach($path as $p) {
            $node = isset($node[$p]) ? $node[$p] : array();
        }

        if(is_array($node) && !$this->assoc($node)) {
            $update = $this->withIds($node);
            $this->updateIn($path, $update);
            return $update;
        }

        return $node;
    }

    function remove($id){
    }

    function assoc($arr) {
        return is_array($arr) && array_keys($arr) !== range(0, count($arr) - 1);
    }

    function withIds($arr){
        $update = array();

        foreach($arr as $k => $v){
            if(!isset($v['id'])){
                $id = str::random(32);
                $v['id'] = $id;
            }
            $update[$v['id']] = $v;
        }

        return $update;
    }
}


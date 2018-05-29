<?php
global $php;

if ($php->factory_key == 'master')
{
    $config = $php->config['redis']['master'];

    if (empty($config['host']))
    {
        $config['host'] = '127.0.0.1';
    }
}
else
{
    $config = $php->config['redis'][$php->factory_key];
    if (empty($config) or empty($config['host']))
    {
        throw new Exception("redis require server host ip.");
    }
}

if (empty($config['port']))
{
    $config['port'] = 6379;
}

if (empty($config["pconnect"]))
{
    $config["pconnect"] = false;
}

if (empty($config['timeout']))
{
    $config['timeout'] = 0.5;
}

$redis = new Redis();
if($config['pconnect'])
{
    $redis->pconnect($config['host'], $config['port'], $config['timeout']);
}
else
{
    $redis->connect($config['host'], $config['port'], $config['timeout']);
}

if (!empty($config['password']))
{
    $redis->auth($config['password']);
}

if (!empty($config['database']))
{
    $redis->select($config['database']);
}
return $redis;
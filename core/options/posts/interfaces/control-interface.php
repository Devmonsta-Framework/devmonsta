<?php
namespace Devmonsta\Options\Posts\Interfaces;
/**
 * Interface for MetaBox controls
 */

interface Control_Interface
{
    public function show($content);
    public function html($content);
}

<?php

namespace Viewflex\Ligero\Base;

use App\Http\Controllers\Controller;
use Viewflex\Ligero\Controllers\HasPublisherUi;
use Viewflex\Ligero\Publishers\HasPublisherSession;
use Viewflex\Ligero\Utility\BootstrapUiTrait;
use Viewflex\Ligero\Utility\RouteHelperTrait;

abstract class BasePublisherController extends Controller
{
    use HasPublisherSession, HasPublisherUi, RouteHelperTrait, BootstrapUiTrait;
    
}

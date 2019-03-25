<?php

namespace Viewflex\Ligero\Base;

use App\Http\Controllers\Controller;

use Viewflex\Ligero\Publishers\HasFluentConfiguration;
use Viewflex\Ligero\Publishers\HasPublisher;
use Viewflex\Ligero\Publishers\HasPublisherSession;
use Viewflex\Ligero\Publishers\HasPublisherUi;
use Viewflex\Ligero\Utility\BootstrapUiTrait;
use Viewflex\Ligero\Utility\RouteHelperTrait;

/**
 * Extend this class, create and configure a Publisher,
 * to perform domain CRUD actions via stateful web UI.
 */
abstract class BasePublisherController extends Controller
{

    use BootstrapUiTrait;
    use HasFluentConfiguration;
    use HasPublisher;
    use HasPublisherSession;
    use HasPublisherUi;
    use RouteHelperTrait;
    
}

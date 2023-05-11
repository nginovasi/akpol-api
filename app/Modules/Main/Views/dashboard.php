<div>

<?php
	
	$session = \Config\Services::session();
	$menu = $session->get('menu');

    echo json_encode($menu);
?>
	<!-- <div class="page-hero page-container " id="page-hero">
	    <div class="padding d-flex">
	        <div class="page-title">
	            <h2 class="text-md text-highlight">Dashboard</h2>
	            <small class="text-muted">Welcome aboard,
	                <strong>Jacqueline Reid</strong>
	            </small>
	        </div>
	        <div class="flex"></div>
	        <div>
	            <a href="https://themeforest.net/item/basik-responsive-bootstrap-web-admin-template/23365964" class="btn btn-md text-muted">
	                <span class="d-none d-sm-inline mx-1">Buy this Item</span>
	                <i data-feather="arrow-right"></i>
	            </a>
	        </div>
	    </div>
	</div>
	<div class="page-content page-container" id="page-content">
	    <div class="padding">
	        <div class="row row-sm sr">
	            <div class="col-md-12 col-lg-8">
	                <div class="row row-sm">
	                    <div class="col-md-8">
	                        <div class="row row-sm">
	                            <div class="col-12">
	                                <div class="card">
	                                    <div class="card-body">
	                                        <div class="row row-sm">
	                                            <div class="col-4">
	                                                <small class="text-muted">Your status</small>
	                                                <div class="mt-2 font-weight-500"><span class="text-info">Pro</span> user</div>
	                                            </div>
	                                            <div class="col-4">
	                                                <small class="text-muted">Your balance</small>
	                                                <div class="text-highlight mt-2 font-weight-500">$3,500</div>
	                                            </div>
	                                            <div class="col-4">
	                                                <small class="text-muted">Next payment</small>
	                                                <div class="mt-2 font-weight-500">15 Nov</div>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="row row-sm">
	                            <div class="col-6 d-flex">
	                                <div class="card flex">
	                                    <div class="card-body">
	                                        <small>Profile complete:
	                                            <strong class="text-primary">65%</strong>
	                                        </small>
	                                        <div class="progress my-3 circle" style="height:6px;">
	                                            <div class="progress-bar circle gd-primary" data-toggle="tooltip" title="65%" style="width: 65%"></div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-6 d-flex">
	                                <div class="card flex">
	                                    <div class="card-body">
	                                        <small>Payment process:
	                                            <strong class="text-primary">25%</strong>
	                                        </small>
	                                        <div class="progress my-3 circle" style="height:6px;">
	                                            <div class="progress-bar circle gd-warning" data-toggle="tooltip" title="25%" style="width: 25%"></div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-md-4 d-flex">
	                        <div class="card flex">
	                            <div class="card-body text-center">
	                                <small class="text-muted">Total Sale &amp; Referral</small>
	                                <div class="pt-3">
	                                    <div style="height: 120px" class="pos-rlt">
	                                        <div class="pos-abt w-100 h-100 d-flex align-items-center justify-content-center">
	                                            <div>
	                                                <div class="text-highlight text-md">
	                                                    <span data-plugin="countTo" data-option="{
									from: 100,
								    to: 39500,
								    refreshInterval: 15,
								    speed: 1000,
								    formatter: function (value, options) {
								      return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
								    }
								    }"></span>
	                                                </div>
	                                                <small class="text-muted">usd</small>
	                                            </div>
	                                        </div>
	                                        <canvas data-plugin="chartjs" id="chart-pie-1"></canvas>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-4">
	                        <div class="card">
	                            <div class="card-body">
	                                <div class="d-md-flex">
	                                    <div class="flex">
	                                        <div class="text-highlight">30%</div>
	                                        <small class="h-1x">Conversion</small>
	                                    </div>
	                                    <div>
	                                        <small class="text-muted">+ 3.5%</small>
	                                    </div>
	                                </div>
	                                <div class="w-50" style="height: 30px;">
	                                    <canvas data-plugin="chartjs" id="chart-line-1">
	                                    </canvas>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-4">
	                        <div class="card">
	                            <div class="card-body">
	                                <div class="d-md-flex">
	                                    <div class="flex">
	                                        <div class="text-highlight">25%</div>
	                                        <small class="h-1x">Search engine</small>
	                                    </div>
	                                    <div>
	                                        <small class="text-muted">- 2.0%</small>
	                                    </div>
	                                </div>
	                                <div class="w-50" style="height: 30px;">
	                                    <canvas data-plugin="chartjs" id="chart-line-2">
	                                    </canvas>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-4">
	                        <div class="card">
	                            <div class="card-body">
	                                <div class="d-md-flex">
	                                    <div class="flex">
	                                        <div class="text-highlight">45%</div>
	                                        <small class="h-1x">Directly</small>
	                                    </div>
	                                    <div>
	                                        <small class="text-muted">+ 4.5%</small>
	                                    </div>
	                                </div>
	                                <div class="w-50" style="height: 30px;">
	                                    <canvas data-plugin="chartjs" id="chart-line-3">
	                                    </canvas>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-md-12 col-lg-4 d-flex">
	                <div class="card flex">
	                    <div class="card-body">
	                        <div class="px-4">
	                            <div id="jqvmap-world" data-plugin="vectorMap" style="height: 200px" class="d-flex align-items-center justify-content-center">
	                                <div class="loading"></div>
	                            </div>
	                            <div class="text-center mb-3">
	                                <small class="text-muted">Your top countries</small>
	                            </div>
	                            <div class="row text-center">
	                                <div class="col">
	                                    <span class="text-primary">USA</span>
	                                    <div class="text-muted text-sm">$1,250</div>
	                                </div>
	                                <div class="col">
	                                    UK
	                                    <div class="text-muted text-sm">$650</div>
	                                </div>
	                                <div class="col">
	                                    India
	                                    <div class="text-muted text-sm">$200</div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-md-4 d-flex">
	                <div class="card flex">
	                    <div class="card-body">
	                        <div class="d-flex align-items-center text-hover-success">
	                            <div class="avatar w-56 m-2 no-shadow gd-success">
	                                <i data-feather="trending-up"></i>
	                            </div>
	                            <div class="px-4 flex">
	                                <div>Weekly top sell</div>
	                                <div class="text-success mt-2">
	                                    + 2.50%
	                                </div>
	                            </div>
	                            <a href="#" class="text-muted">
	                                <i data-feather="arrow-right"></i>
	                            </a>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-md-8 d-flex">
	                <div class="card flex">
	                    <div class="card-body">
	                        <div class="row row-sm">
	                            <div class="col-sm-6">
	                                <div class="mb-2">
	                                    <small class="text-muted">Task statistics</small>
	                                </div>
	                                <div class="row row-sm">
	                                    <div class="col-4">
	                                        <div class="text-highlight text-md">52</div>
	                                        <small>Tasks</small>
	                                    </div>
	                                    <div class="col-4">
	                                        <div class="text-danger text-md">+15</div>
	                                        <small>Added</small>
	                                    </div>
	                                    <div class="col-4">
	                                        <div class="text-md">45.5%</div>
	                                        <small>Remain</small>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-12 col-sm-6">
	                                <div class="mb-2 mt-2 mt-sm-0">
	                                    <small class="text-muted">This week</small>
	                                </div>
	                                <div>Task completion</div>
	                                <div class="progress no-bg mt-2 align-items-center circle" style="height:6px;">
	                                    <div class="progress-bar circle gd-danger" style="width: 65%"></div>
	                                    <span class="mx-2">65%</span>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-md-4">
	                <div class="row row-sm">
	                    <div class="col-6">
	                        <div class="card">
	                            <div class="card-body">
	                                <div class="pos-rlt" style="height: 78px">
	                                    <div class="pos-abt w-100 h-100 d-flex align-items-center justify-content-center">
	                                        <small>35%</small>
	                                    </div>
	                                    <canvas data-plugin="chartjs" id="chart-pie-2"></canvas>
	                                </div>
	                                <div class="px-3 pt-3 text-center">
	                                    <small>Weekly</small>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-6">
	                        <div class="card">
	                            <div class="card-body">
	                                <div class="pos-rlt" style="height: 78px">
	                                    <div class="pos-abt w-100 h-100 d-flex align-items-center justify-content-center">
	                                        <small>25%</small>
	                                    </div>
	                                    <canvas data-plugin="chartjs" id="chart-pie-3"></canvas>
	                                </div>
	                                <div class="px-3 pt-3 text-center">
	                                    <small>Monthly</small>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="col-12">
	                        <div class="card pb-3">
	                            <div class="p-3-4">
	                                <div class="d-flex">
	                                    <div>
	                                        <div>Upcoming tasks</div>
	                                        <small class="text-muted">Active: 9</small>
	                                    </div>
	                                    <span class="flex"></span>
	                                    <div>
	                                        <div class="btn-group-toggle" data-toggle="buttons">
	                                            <label class="btn">
	                                                <input type="radio" name="options"> 1h
	                                            </label>
	                                            <label class="btn active">
	                                                <input type="radio" name="options"> 1d
	                                            </label>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="list list-row">
	                                <div class="list-item " data-id="16">
	                                    <div>
	                                        <label class="ui-check m-0 ui-check-rounded ui-check-md">
	                                            <input type="checkbox" name="id" value="16">
	                                            <i></i>
	                                        </label>
	                                    </div>
	                                    <div class="flex">
	                                        <a href="#" class="item-title text-color h-1x">AI Could Uber</a>
	                                        <div class="item-except text-muted text-sm h-1x">
	                                            When it comes to artificial intelligence, little things can add up in big ways
	                                        </div>
	                                    </div>
	                                    <div>
	                                        <div class="item-action dropdown">
	                                            <a href="#" data-toggle="dropdown" class="text-muted">
	                                                <i data-feather="more-vertical"></i>
	                                            </a>
	                                            <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                                <a class="dropdown-item" href="#">
	                                                    See detail
	                                                </a>
	                                                <a class="dropdown-item download">
	                                                    Download
	                                                </a>
	                                                <a class="dropdown-item edit">
	                                                    Edit
	                                                </a>
	                                                <div class="dropdown-divider"></div>
	                                                <a class="dropdown-item trash">
	                                                    Delete item
	                                                </a>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="list-item " data-id="9">
	                                    <div>
	                                        <label class="ui-check m-0 ui-check-rounded ui-check-md">
	                                            <input type="checkbox" name="id" value="9">
	                                            <i></i>
	                                        </label>
	                                    </div>
	                                    <div class="flex">
	                                        <a href="#" class="item-title text-color h-1x">Web App develop tutorial</a>
	                                        <div class="item-except text-muted text-sm h-1x">
	                                            Build a progressive web app using jQuery
	                                        </div>
	                                    </div>
	                                    <div>
	                                        <div class="item-action dropdown">
	                                            <a href="#" data-toggle="dropdown" class="text-muted">
	                                                <i data-feather="more-vertical"></i>
	                                            </a>
	                                            <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                                <a class="dropdown-item" href="#">
	                                                    See detail
	                                                </a>
	                                                <a class="dropdown-item download">
	                                                    Download
	                                                </a>
	                                                <a class="dropdown-item edit">
	                                                    Edit
	                                                </a>
	                                                <div class="dropdown-divider"></div>
	                                                <a class="dropdown-item trash">
	                                                    Delete item
	                                                </a>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                                <div class="list-item " data-id="8">
	                                    <div>
	                                        <label class="ui-check m-0 ui-check-rounded ui-check-md">
	                                            <input type="checkbox" name="id" value="8">
	                                            <i></i>
	                                        </label>
	                                    </div>
	                                    <div class="flex">
	                                        <a href="#" class="item-title text-color h-1x">DEV DAY 2018</a>
	                                        <div class="item-except text-muted text-sm h-1x">
	                                            Working on a research piece on the history of vocational schools in America.
	                                        </div>
	                                    </div>
	                                    <div>
	                                        <div class="item-action dropdown">
	                                            <a href="#" data-toggle="dropdown" class="text-muted">
	                                                <i data-feather="more-vertical"></i>
	                                            </a>
	                                            <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                                <a class="dropdown-item" href="#">
	                                                    See detail
	                                                </a>
	                                                <a class="dropdown-item download">
	                                                    Download
	                                                </a>
	                                                <a class="dropdown-item edit">
	                                                    Edit
	                                                </a>
	                                                <div class="dropdown-divider"></div>
	                                                <a class="dropdown-item trash">
	                                                    Delete item
	                                                </a>
	                                            </div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-md-8">
	                <div class="card">
	                    <div class="p-3-4">
	                        <div class="d-flex mb-3">
	                            <div>
	                                <div>Summary</div>
	                                <small class="text-muted">All mentions: 12,340</small>
	                            </div>
	                            <span class="flex"></span>
	                            <div>
	                                <div class="btn-group btn-group-toggle" id="btn_l_4" data-toggle="buttons">
	                                    <label class="btn active">
	                                        <input type="radio" name="options"> Month
	                                    </label>
	                                    <label class="btn">
	                                        <input type="radio" name="options"> Week
	                                    </label>
	                                </div>
	                            </div>
	                        </div>
	                        <div class="p-2" style="height: 364px">
	                            <canvas data-plugin="chartjs" id="chart-line-4"></canvas>
	                        </div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-md-7">
	                <div class="card">
	                    <div class="p-3-4">
	                        <div class="d-flex">
	                            <div>
	                                <div>Top groups</div>
	                                <small class="text-muted">Total: 230</small>
	                            </div>
	                            <span class="flex"></span>
	                            <div>
	                                <a href="#" class="btn btn-sm btn-white">More</a>
	                            </div>
	                        </div>
	                    </div>
	                    <table class="table table-theme v-middle m-0">
	                        <tbody>
	                            <tr class=" " data-id="16">
	                                <td style="min-width:30px;text-align:center">
	                                    0
	                                </td>
	                                <td>
	                                    <div class="avatar-group ">
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Urna">
	                                            <img src="../assets/img/a15.jpg" alt=".">
	                                        </a>
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Eu">
	                                            <img src="../assets/img/a11.jpg" alt=".">
	                                        </a>
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Odio">
	                                            <img src="../assets/img/a13.jpg" alt=".">
	                                        </a>
	                                    </div>
	                                </td>
	                                <td class="flex">
	                                    <a href="page.invoice.detail.html" class="item-company ajax h-1x">
	                                        Microsoft
	                                    </a>
	                                    <div class="item-mail text-muted h-1x d-none d-sm-block">
	                                        frances-stewart@microsoft.com
	                                    </div>
	                                </td>
	                                <td>
	                                    <span class="item-amount d-none d-sm-block text-sm ">
	              200
	            </span>
	                                </td>
	                                <td>
	                                    <div class="item-action dropdown">
	                                        <a href="#" data-toggle="dropdown" class="text-muted">
	                                            <i data-feather="more-vertical"></i>
	                                        </a>
	                                        <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                            <a class="dropdown-item" href="#">
	                                                See detail
	                                            </a>
	                                            <a class="dropdown-item download">
	                                                Download
	                                            </a>
	                                            <a class="dropdown-item edit">
	                                                Edit
	                                            </a>
	                                            <div class="dropdown-divider"></div>
	                                            <a class="dropdown-item trash">
	                                                Delete item
	                                            </a>
	                                        </div>
	                                    </div>
	                                </td>
	                            </tr>
	                            <tr class=" " data-id="4">
	                                <td style="min-width:30px;text-align:center">
	                                    1
	                                </td>
	                                <td>
	                                    <div class="avatar-group ">
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Libero">
	                                            <img src="../assets/img/a3.jpg" alt=".">
	                                        </a>
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Consequat">
	                                            <img src="../assets/img/a15.jpg" alt=".">
	                                        </a>
	                                    </div>
	                                </td>
	                                <td class="flex">
	                                    <a href="page.invoice.detail.html" class="item-company ajax h-1x">
	                                        GE
	                                    </a>
	                                    <div class="item-mail text-muted h-1x d-none d-sm-block">
	                                        billy-johnston@ge.com
	                                    </div>
	                                </td>
	                                <td>
	                                    <span class="item-amount d-none d-sm-block text-sm ">
	              20
	            </span>
	                                </td>
	                                <td>
	                                    <div class="item-action dropdown">
	                                        <a href="#" data-toggle="dropdown" class="text-muted">
	                                            <i data-feather="more-vertical"></i>
	                                        </a>
	                                        <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                            <a class="dropdown-item" href="#">
	                                                See detail
	                                            </a>
	                                            <a class="dropdown-item download">
	                                                Download
	                                            </a>
	                                            <a class="dropdown-item edit">
	                                                Edit
	                                            </a>
	                                            <div class="dropdown-divider"></div>
	                                            <a class="dropdown-item trash">
	                                                Delete item
	                                            </a>
	                                        </div>
	                                    </div>
	                                </td>
	                            </tr>
	                            <tr class=" " data-id="17">
	                                <td style="min-width:30px;text-align:center">
	                                    2
	                                </td>
	                                <td>
	                                    <div class="avatar-group ">
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Adipiscing">
	                                            <img src="../assets/img/a6.jpg" alt=".">
	                                        </a>
	                                    </div>
	                                </td>
	                                <td class="flex">
	                                    <a href="page.invoice.detail.html" class="item-company ajax h-1x">
	                                        Alibaba
	                                    </a>
	                                    <div class="item-mail text-muted h-1x d-none d-sm-block">
	                                        alan-mendez@alibaba.com
	                                    </div>
	                                </td>
	                                <td>
	                                    <span class="item-amount d-none d-sm-block text-sm ">
	              320
	            </span>
	                                </td>
	                                <td>
	                                    <div class="item-action dropdown">
	                                        <a href="#" data-toggle="dropdown" class="text-muted">
	                                            <i data-feather="more-vertical"></i>
	                                        </a>
	                                        <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                            <a class="dropdown-item" href="#">
	                                                See detail
	                                            </a>
	                                            <a class="dropdown-item download">
	                                                Download
	                                            </a>
	                                            <a class="dropdown-item edit">
	                                                Edit
	                                            </a>
	                                            <div class="dropdown-divider"></div>
	                                            <a class="dropdown-item trash">
	                                                Delete item
	                                            </a>
	                                        </div>
	                                    </div>
	                                </td>
	                            </tr>
	                            <tr class=" " data-id="19">
	                                <td style="min-width:30px;text-align:center">
	                                    3
	                                </td>
	                                <td>
	                                    <div class="avatar-group ">
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Erat">
	                                            <img src="../assets/img/a15.jpg" alt=".">
	                                        </a>
	                                    </div>
	                                </td>
	                                <td class="flex">
	                                    <a href="page.invoice.detail.html" class="item-company ajax h-1x">
	                                        AI
	                                    </a>
	                                    <div class="item-mail text-muted h-1x d-none d-sm-block">
	                                        tiffany-baker@ai.com
	                                    </div>
	                                </td>
	                                <td>
	                                    <span class="item-amount d-none d-sm-block text-sm ">
	              320
	            </span>
	                                </td>
	                                <td>
	                                    <div class="item-action dropdown">
	                                        <a href="#" data-toggle="dropdown" class="text-muted">
	                                            <i data-feather="more-vertical"></i>
	                                        </a>
	                                        <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                            <a class="dropdown-item" href="#">
	                                                See detail
	                                            </a>
	                                            <a class="dropdown-item download">
	                                                Download
	                                            </a>
	                                            <a class="dropdown-item edit">
	                                                Edit
	                                            </a>
	                                            <div class="dropdown-divider"></div>
	                                            <a class="dropdown-item trash">
	                                                Delete item
	                                            </a>
	                                        </div>
	                                    </div>
	                                </td>
	                            </tr>
	                            <tr class=" " data-id="15">
	                                <td style="min-width:30px;text-align:center">
	                                    4
	                                </td>
	                                <td>
	                                    <div class="avatar-group ">
	                                        <a href="#" class="avatar ajax w-32" data-toggle="tooltip" title="Lobortis">
	                                            <img src="../assets/img/a6.jpg" alt=".">
	                                        </a>
	                                    </div>
	                                </td>
	                                <td class="flex">
	                                    <a href="page.invoice.detail.html" class="item-company ajax h-1x">
	                                        Google
	                                    </a>
	                                    <div class="item-mail text-muted h-1x d-none d-sm-block">
	                                        jean-armstrong@google.com
	                                    </div>
	                                </td>
	                                <td>
	                                    <span class="item-amount d-none d-sm-block text-sm ">
	              240
	            </span>
	                                </td>
	                                <td>
	                                    <div class="item-action dropdown">
	                                        <a href="#" data-toggle="dropdown" class="text-muted">
	                                            <i data-feather="more-vertical"></i>
	                                        </a>
	                                        <div class="dropdown-menu dropdown-menu-right bg-black" role="menu">
	                                            <a class="dropdown-item" href="#">
	                                                See detail
	                                            </a>
	                                            <a class="dropdown-item download">
	                                                Download
	                                            </a>
	                                            <a class="dropdown-item edit">
	                                                Edit
	                                            </a>
	                                            <div class="dropdown-divider"></div>
	                                            <a class="dropdown-item trash">
	                                                Delete item
	                                            </a>
	                                        </div>
	                                    </div>
	                                </td>
	                            </tr>
	                        </tbody>
	                    </table>
	                </div>
	            </div>
	            <div class="col-md-5 d-flex">
	                <div class="card flex">
	                    <div class="p-3-4">
	                        <div class="d-flex">
	                            <div>
	                                <div>Courses</div>
	                                <small class="text-muted">on-going: 12</small>
	                            </div>
	                            <span class="flex"></span>
	                            <div>
	                                <a href="#" class="p-1 text-muted">
	                                    <i data-feather="more-horizontal"></i>
	                                </a>
	                            </div>
	                        </div>
	                    </div>
	                    <div class="list list-row">
	                        <div class="list-item " data-id="17">
	                            <div>
	                                <span class="badge badge-circle text-warning"></span>
	                            </div>
	                            <div class="flex">
	                                <a href="#" class="item-title text-color h-1x">AI Could Uber</a>
	                            </div>
	                            <div>
	                                <span class="item-amount d-none d-sm-block text-sm text-muted">
		              53
		            </span>
	                            </div>
	                        </div>
	                        <div class="list-item " data-id="9">
	                            <div>
	                                <span class="badge badge-circle text-info"></span>
	                            </div>
	                            <div class="flex">
	                                <a href="#" class="item-title text-color h-1x">Web App develop tutorial</a>
	                            </div>
	                            <div>
	                                <span class="item-amount d-none d-sm-block text-sm text-muted">
		              20
		            </span>
	                            </div>
	                        </div>
	                        <div class="list-item " data-id="13">
	                            <div>
	                                <span class="badge badge-circle text-primary"></span>
	                            </div>
	                            <div class="flex">
	                                <a href="#" class="item-title text-color h-1x">Feed Reader</a>
	                            </div>
	                            <div>
	                                <span class="item-amount d-none d-sm-block text-sm text-muted">
		              20
		            </span>
	                            </div>
	                        </div>
	                        <div class="list-item " data-id="3">
	                            <div>
	                                <span class="badge badge-circle text-primary"></span>
	                            </div>
	                            <div class="flex">
	                                <a href="#" class="item-title text-color h-1x">Html5 dashboard conference</a>
	                            </div>
	                            <div>
	                                <span class="item-amount d-none d-sm-block text-sm text-muted">
		              50
		            </span>
	                            </div>
	                        </div>
	                        <div class="list-item " data-id="2">
	                            <div>
	                                <span class="badge badge-circle text-primary"></span>
	                            </div>
	                            <div class="flex">
	                                <a href="#" class="item-title text-color h-1x">Data analytics application</a>
	                            </div>
	                            <div>
	                                <span class="item-amount d-none d-sm-block text-sm text-muted">
		              4
		            </span>
	                            </div>
	                        </div>
	                        <div class="list-item " data-id="4">
	                            <div>
	                                <span class="badge badge-circle text-success"></span>
	                            </div>
	                            <div class="flex">
	                                <a href="#" class="item-title text-color h-1x">Open source project public release</a>
	                            </div>
	                            <div>
	                                <span class="item-amount d-none d-sm-block text-sm text-muted">
		              7
		            </span>
	                            </div>
	                        </div>
	                        <div class="list-item " data-id="1">
	                            <div>
	                                <span class="badge badge-circle text-primary"></span>
	                            </div>
	                            <div class="flex">
	                                <a href="#" class="item-title text-color h-1x">WordPress dashboard redesign</a>
	                            </div>
	                            <div>
	                                <span class="item-amount d-none d-sm-block text-sm text-muted">
		              5
		            </span>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div> -->
</div>

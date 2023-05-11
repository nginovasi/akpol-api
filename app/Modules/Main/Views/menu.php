 <!--############ Aside START-->
<div id="aside" class="page-sidenav no-shrink bg-light nav-dropdown fade" aria-hidden="true">
    <div class="sidenav h-100 modal-dialog bg-light">
        <!-- sidenav top -->
        <div class="navbar">
            <!-- brand -->
            <a href="<?=base_url()?>/main" class="navbar-brand ">
                <svg width="32" height="32" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor">
                    <g class="loading-spin" style="transform-origin: 256px 256px">
                        <path d="M200.043 106.067c-40.631 15.171-73.434 46.382-90.717 85.933H256l-55.957-85.933zM412.797 288A160.723 160.723 0 0 0 416 256c0-36.624-12.314-70.367-33.016-97.334L311 288h101.797zM359.973 134.395C332.007 110.461 295.694 96 256 96c-7.966 0-15.794.591-23.448 1.715L310.852 224l49.121-89.605zM99.204 224A160.65 160.65 0 0 0 96 256c0 36.639 12.324 70.394 33.041 97.366L201 224H99.204zM311.959 405.932c40.631-15.171 73.433-46.382 90.715-85.932H256l55.959 85.932zM152.046 377.621C180.009 401.545 216.314 416 256 416c7.969 0 15.799-.592 23.456-1.716L201.164 288l-49.118 89.621z"></path>
                    </g>
                </svg>
                <!-- <img src="../assets/img/logo.png" alt="..."> -->
                <span class="hidden-folded d-inline l-s-n-1x ">E-SLDC</span>
            </a>
            <!-- / brand -->
        </div>
        <!-- Flex nav content -->
        <div class="flex scrollable hover">
            <div class="nav-active-text-primary" data-nav>
                <ul class="nav bg">
                    <li class="nav-header hidden-folded">
                        <span class="text-muted">Main</span>
                    </li>
                    <?php $module_active = uri_segment(0); $menu_active = uri_segment(1);?>
                    <li class="<?=(($module_active == 'main') ? 'active' : '')?>">
                        <a href="<?=base_url()?>/main">
                            <span class="nav-icon "><i data-feather="home"></i></span>
                            <span class="nav-text">Dashboard </span>
                        </a>
                    </li>

                    <?php
                        $session = \Config\Services::session();

                        function group_by($array, $by){
                            $groups = array();

                            foreach ($array as $key => $value) {
                                $groups[$value->$by][] = $value;
                            }

                            return $groups;
                        }

                        $module = group_by($session->get('menu'), 'module_name');

                        foreach ($module as $key => $_module) { 

                            echo '<li class="nav-header hidden-folded">
                                    <span class="text-muted">'.$key.'</span>
                                 </li>';

                            $grouped = group_by($_module, 'menu_parent');

                            foreach ($grouped as $_key => $_grouped) {
                                if($_key == ""){
                                    foreach ($_grouped as $__key => $menu) {
                                        echo '<li class="'.(($menu_active == $menu->menu_url) ? 'active' : '').'">
                                                    <a href="'.base_url().'/'.$menu->module_url.'/'.$menu->menu_url.'">
                                                        <span class="nav-icon"><i data-feather="chevrons-right"></i></span>
                                                        <span class="nav-text">'.$menu->menu_name.'</span>
                                                    </a>
                                            </li>';
                                    }
                                }else{
                                    echo '<li class="'.((count(array_filter($_grouped, function($arr) use ($menu_active) { return strtolower($arr->menu_url) == strtolower($menu_active); })) > 0) ? 'active' : '').'">
                                            <a href="#" class="">
                                                <span class="nav-icon"><i data-feather="chevrons-right"></i></span>
                                                <span class="nav-text">'.$_key.'</span>
                                                <span class="nav-caret"></span>
                                            </a>
                                            <ul class="nav-sub nav-mega">';

                                            foreach ($_grouped as $__key => $menu) {
                                                echo '<li class="'.(($menu_active == $menu->module_url) ? 'active' : '').'">
                                                        <a href="'.base_url().'/'.$menu->module_url.'/'.$menu->menu_url.'" class="">
                                                            <span class="nav-text">'.$menu->menu_name.'</span>
                                                        </a>
                                                    </li>';
                                            }

                                            
                                    echo    '</ul>
                                        </li>';
                                }
                            }
                        } 
                    ?>
                </ul>
            </div>
        </div>
        <!-- sidenav bottom -->
        <div class="no-shrink ">
            <div class="p-3 d-flex align-items-center">
                <div class="text-sm hidden-folded text-muted">
                    Trial: 35%
                </div>
                <div class="progress mx-2 flex" style="height:4px;">
                    <div class="progress-bar gd-success" style="width: 35%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ############ Aside END -->
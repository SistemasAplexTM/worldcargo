@extends('layouts.app')
@section('title', 'Inicio')
@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>
                @lang('general.home')
            </h2>
        </div>
    </div>
@endsection
@section('content')
    @if (Auth::user()->isRole('admin'))
        <div class="row" id="home">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInRight" style="padding-top: 0px;">
                    <div class="row">
                        <div class="col-lg-12" style="margin-bottom: 10px;">
                            <div class="col-lg-6">
                                <div class=" feed-activity-list">
                                    <div class="feed-element">
                                        <h1>
                                            Principal
                                        </h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class=" feed-activity-list">
                                    <div class="feed-element">
                                        <h1>
                                            @lang('general.users')
                                        </h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="col-lg-3 text-center">
                                <button class="btn btn-warning dim btn-large-dim btn-outline btn-inicio" type="button"
                                    id="documento">
                                    <i class="fa fa-box-open">
                                    </i>
                                </button>
                                <div style="font-size: 20px;">
                                    @lang('general.warehouse')
                                </div>
                            </div>
                            <div class="col-lg-3 text-center">
                                <button class="btn btn-primary dim btn-large-dim btn-outline btn-inicio" type="button"
                                    id="master">
                                    <i class="fa fa-paste">
                                    </i>
                                </button>
                                <div style="font-size: 20px;">
                                    Master
                                </div>
                            </div>
                            <div class="col-lg-3 text-center">
                                <button class="btn btn-default dim btn-large-dim btn-outline btn-inicio" type="button"
                                    id="tracking">
                                    <i class="fa fa-cubes">
                                    </i>
                                </button>
                                <div style="font-size: 20px;">
                                    @lang('general.tracking')
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="col-lg-3 text-center">
                                <button class="btn btn-success dim btn-large-dim btn-outline btn-inicio" type="button"
                                    id="shipper">
                                    <i class="far fa-user-circle">
                                    </i>
                                </button>
                                <div style="font-size: 20px;">
                                    @lang('general.shipper')
                                </div>
                            </div>
                            <div class="col-lg-3 text-center">
                                <button class="btn btn-info dim btn-large-dim btn-outline btn-inicio" type="button"
                                    id="consignee">
                                    <i class="fas fa-user-circle">
                                    </i>
                                </button>
                                <div style="font-size: 20px;">
                                    @lang('general.consignee')
                                </div>
                            </div>

                            <div class="col-lg-3 text-center">
                                <button class="btn btn-primary dim btn-large-dim btn-outline btn-inicio" type="button"
                                    id="backup">
                                    <i class="fa fa-cloud-download-alt">
                                    </i>
                                </button>
                                <div style="font-size: 20px;">
                                    @lang('general.backup')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12" style="margin-top:50px;">
                            <div class="col-lg-3">
                                <div class="ibox float-e-margins">
                                    <div class="ibox-title">
                                        <span class="label label-success pull-right">Total Registros</span>
                                        <h5>Consignees</h5>
                                    </div>
                                    <div class="ibox-content">
                                        <h1 class="no-margins">@{{ all }}</h1>
                                        <div class="stat-percent font-bold text-success">@{{ web }} <i
                                                class="fa fa-user"></i></div>
                                        <small>Registros Mes Pagina</small>
                                        <br>
                                        <div class="stat-percent font-bold text-success">@{{ sistem }} <i
                                                class="fa fa-user"></i></div>
                                        <small>Registros Mes Sistema</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="row">
                    <div class="col-lg-12" style="margin-bottom: 10px;margin-top: 30px;">
                        <div class="col-lg-6">
                            <div class=" feed-activity-list">
                                <div class="feed-element">
                                    <h1>
                                        Ficheros
                                    </h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="col-lg-3 text-center">
                            <button class="btn btn-default dim btn-large-dim btn-outline btn-inicio" type="button" id="mantenimiento">
                                <i class="fa fa-wrench">
                                </i>
                            </button>
                            <div style="font-size: 20px;">
                                Mantenimiento
                            </div>
                        </div>
                        <div class="col-lg-3 text-center">
                            <button class="btn btn-danger dim btn-large-dim btn-outline btn-inicio" type="button" id="administracion">
                                <i class="fa fa-cogs">
                                </i>
                            </button>
                            <div style="font-size: 20px;">
                                Administración
                            </div>
                        </div>
                    </div>
                </div> -->
                </div>
            </div>
        </div>
    @endif
@endsection
@section('scripts')
    <script src="{{ asset('js/templates/home.js') }}"></script>
@endsection

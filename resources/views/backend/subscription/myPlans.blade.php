@extends('backend.app')

@section('title', 'All Services')

@push('style')
    <link href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" rel="stylesheet" />
    <style>
        .dropify-wrapper {
            width: 160px;
        }

        #data-table th,
        #data-table td {
            text-align: center !important;
            vertical-align: middle !important;
        }
    </style>
@endpush
@section('content')
    <main class="app-content content">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Basic/Monthly Package</h4>
                        <p>$9.99</p>
                    </div>
                    <div class="card-body">
                        <table id="data-table" class="table table-bordered"
                            style="width: 100%">
                            <tbody>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Premimum/Yearly Package</h4>
                        <p>$19.99</p>
                    </div>
                    <div class="card-body">
                        <table id="data-table" class="table table-bordered"
                            style="width: 100%">
                            <tbody>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                                <tr>
                                    <td><p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Beatae, aspernatur.</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>



@endsection

@push('script')

@endpush


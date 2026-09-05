@extends('FrontEnd.layouts.master')
@section('content')


  <!-- Theme style -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ env('APP_URL') }}/dist/css/skins/_all-skins.min.css">



  <script src="http://demo.itsolutionstuff.com/plugin/croppie.js"></script>
  <link rel="stylesheet" href="http://demo.itsolutionstuff.com/plugin/bootstrap-3.min.css">
  <link rel="stylesheet" href="http://demo.itsolutionstuff.com/plugin/croppie.css">


  <section class="content-header">
    <h1>
      Product
      <small>Management</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ env('APP_URL') }}/dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ env('APP_URL') }}/dashboard/product">Product Management</a></li>
      <li><a href="#">Product Image Crop</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title">Product Image Crop</h3>

        <div class="box-tools pull-right">

          <a href="{{ env('APP_URL') }}/dashboard/product/edit/{{$pid}}" class="btn btn-danger btn-xs"><i class="fa fa-arrow-left"></i></a>

        </div>
      </div>

            <div class="panel panel-default">

                <div class="panel-body">


                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div id="upload-demo" style="width:550px">

                            </div>
                        </div>
                        <div class="col-md-4" style="padding-top:30px; padding-left:100px;">
                            <strong>Select Image:</strong>
                            <br/>
                            <input type="file" id="upload">
                            <br/>
                            <button class="btn btn-success upload-result">Crop & Upload Image</button>
                        </div>


                        <div class="col-md-4" style="">
                            <div id="upload-demo-i" style="background:#e1e1e1;width:500px;padding:30px;height:500px;margin-top:30px"></div>
                        </div>
                    </div>


                </div>
            </div>


        <script type="text/javascript">


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });


            $uploadCrop = $('#upload-demo').croppie({
                enableExif: true,
                url:'<?php echo env('APP_URL'); ?>/<?php echo $img->filename; ?>',
                viewport: {
                    width: 400,
                    height: 400,
                    type: 'square'
                },
                boundary: {
                    width: 500,
                    height: 500
                }
            });


            $('#upload').on('change', function () {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $uploadCrop.croppie('bind', {
                        url: e.target.result
                    }).then(function(){
                        console.log('jQuery bind complete');
                    });
                }
                reader.readAsDataURL(this.files[0]);
            });


            $('.upload-result').on('click', function (ev) {
                var baseurl='<?php echo env('APP_URL'); ?>';
                $uploadCrop.croppie('result', {
                    type: 'canvas',
                    size: 'viewport'
                }).then(function (resp) {
                    $.ajax({
                        url: baseurl+"/dashboard/image-crop",
                        type: "POST",
                        data: {"image":resp, '_token': '<?php echo csrf_token(); ?>', 'img': '<?php echo $img->id; ?>', 'pid': '<?php echo $pid; ?>'},
                        success: function (data) {
                            html = '<img src="' + resp + '" />';
                            $("#upload-demo-i").html(html);
                        }
                    });
                });
            });


        </script>




    </div>
  </section>

@endsection


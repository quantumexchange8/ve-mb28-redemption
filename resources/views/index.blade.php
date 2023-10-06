<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Redemption - VE-MB28-Redemption</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<div id="auth" class="d-flex justify-content-center" style="background-color:#e6f3ff;" >
    <div class="p-4">
        <div >
            <h1 class="auth-title">VE-MB28-Redemption</h1>
            <p class="auth-subtitle mb-5">Enter your redemption code and email below to redeem it.</p>
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-3" id="test">Code Redemption Form</h2>
                    <form action="{{ route('redeem_code') }}" method="POST" id="redemption-form">
                        @csrf
                        <div class="form-group position-relative has-icon-left" id="test2">
                            <input type="text" class="form-control form-control-xl redemption_code" name="redemption_code" id="redemption_code" placeholder="Redemption Code">
                            <div class="form-control-icon">
                                <i class="bi bi-gift"></i>
                            </div>
                        </div>
                        <span class="redemption_code_error text-danger mb-2"></span>
                        <div class="form-group position-relative has-icon-left mt-4">
                            <input type="text" class="form-control form-control-xl email" name="email" id="email" placeholder="Email">
                            <div class="form-control-icon">
                                <i class="bi bi-envelope"></i>
                            </div>
                        </div>
                        <span class="email_error text-danger mb-2"></span>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" id="submitButton">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

    <script>
        $(document).ready(function() {
            // const btn = document.getElementById('submitButton');
            $('#redemption-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this)
                // btn.innerHTML = 'Button clicked';
                $.ajax({
                    method:$(this).attr('method'),
                    url:$(this).attr('action'),
                    data:new FormData(this),
                    processData:false,
                    dataType:'json',
                    contentType:false,
                    beforeSend:function (){
                        form.find('span.error-text').text('');
                    },
                    success: function(data) {
                        console.log(data)
                        if(data.status === 0) {
                            $.each(data.error, function (prefix, val){
                                $('span.'+prefix+'_error').text(val[0]);
                                $('.'+prefix).addClass('border-danger');
                            });
                        } else if (data.status == 2) {
                            Swal.fire({
                                html: data.msg,
                                icon: 'error',
                                showConfirmButton: false,
                                showCloseButton: true,
                                toast:true,
                                target: document.getElementById('test'),
                                position: 'top',
                                customClass: {                      
                                    container: 'position-relative'
                                },
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                html: data.msg + '<br/>' + data.msgSerial + '<br/>' + data.msgDate,
                                icon: 'success',
                                showConfirmButton: false,
                                showCloseButton: true,
                                toast:true,
                                target: document.getElementById('test'),
                                position: 'top',
                                customClass: {                      
                                    container: 'position-relative'
                                },
                            }).then(function() {
                                location.reload();
                            });
                        }
                    },
                    error: function(error) {
                        console.log(error)
                        Swal.fire({
                            title: 'Error!',
                            html: "Something went wrong",
                            icon: 'error',
                            confirmButtonText: 'OK',
                            timerProgressBar: false,
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>

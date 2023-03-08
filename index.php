<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mpesa stk push</title>
</head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
<link rel="stylesheet" href="assets/style.css">
<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>
<body>

<div class="container-fluid">
	<div class="row d-flex justify-content-center">
		<div class="col-sm-12">
			<div class="card mx-auto">
				<p class="heading">PAYMENT DETAILS</p>
					<form class="card-details " method="POST" action="./mpesa.php" id="form">
						<div class="form-group mb-0">
                                <div class="feedback" id="feedback"></div>
								<p class="text-warning mb-0">Phone Number</p> 
                          		<input type="text" name="phone-num" placeholder="254" size="17" id="cno" minlength="12" maxlength="12" id="phone">
								<img src="./assets/mpesa.png" width="64px" height="60px" />
                        </div>

                        <div class="form-group">
                            <p class="text-warning mb-0">Amount</p> <input type="text" name="amount" placeholder="" size="17" id="amount">
                        </div>
                        <div class="form-group pt-2">
                        	<div class="row d-flex">

                        		<div class="col-sm-7 pt-0">
                        			<button type="submit" class="btn btn-primary" id="pay" class="pay-button" name="pay">  Pay<i class="fas fa-arrow-right px-3 py-2"></i></button>
                        		</div>
                        	</div>
                        </div>		
					</form>
			</div>
		</div>
	</div>
</div>
<script>
   $(() => {
        $("#pay").on('click', async (e) => {
            e.preventDefault()

            $("#pay").text('Please wait...').attr('disabled', true)
            const form = $('#form').serializeArray()

            var indexed_array = {};
            $.map(form, function(n, i) {
                indexed_array[n['name']] = n['value'];
            });

            const _response = await fetch('./mpesa.php', {
                method: 'post',
                body: JSON.stringify(indexed_array),
                mode: 'no-cors',
            })

            const response = await _response.json()
            $("#pay").text('Pay').attr('disabled', false)
            $("#pay").html(`Pay <i class="fas fa-arrow-right px-3 py-2"></i>`).attr('disabled', false)

            if (response && response.ResponseCode == 0) {
                $('#feedback').html(`<p class='alert alert-success'>${response.CustomerMessage}</p>`)
            } else {
                $('#feedback').html(`<p class='alert alert-danger'>Error! ${response.errorMessage}</p>`)
            }
        })
    })
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">Dashboard Marathon Myanmar
				</div>

				<div class="card-body">
					@if (session('status'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>{{ session('status') }}</strong>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					@endif
					<p>You are logged in!</p>
					{{-- @php
						$firstStringCharacter = substr("hello", 0, 2);
						echo $firstStringCharacter;
					@endphp --}}

					<a class="btn btn-warning" href="{{ route('export') }}">Export Main Data</a>

					<p>Import Excel data to database</p>

					@if(session()->has('message'))
					<div class="alert alert-success">
						{{ session()->get('message') }}
					</div>
					@endif

					<form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="row">
							<div class="col-md-10">

								<input type="file" name="file" class="form-control">
								<br>
								<div class="row">
									<div class="col-md-4">
										<h5>Master Records</h5>
										<input type="checkbox" name="Staff" value="Staff"> Staff
										<br><input type="checkbox" name="Agent" value="Agent"> Agent
										<br><input type="checkbox" name="GlobalScale" value="GlobalScale"> GlobalScale
										<br><input type="checkbox" name="City" value="City"> City
										<br><input type="checkbox" name="Zone" value="Zone"> Zone
										<br><input type="checkbox" name="Bus" value="Bus"> Bus
										<br><input type="checkbox" name="BusStation" value="BusStation"> BusStation
										<br><input type="checkbox" name="Gate" value="Gate"> Gate

									</div>
									<div class="col-md-4">
										<h5>Routes/Door to Door</h5>
										<input type="checkbox" name="Route" value="Route"> Route
										<br><input type="checkbox" name="DoorToDoor" value="DoorToDoor"> DoorToDoor
									</div>
									<div class="col-md-4">
										<h5>Merchant</h5>
										<input type="checkbox" name="Merchant" value="Merchant"> Merchant
										<br><input type="checkbox" name="MerchantAssociate" value="MerchantAssociate"> MerchantAssociate
										<br><input type="checkbox" name="ContactAssociate" value="ContactAssociate"> ContactAssociate
										{{-- <br> --}}
										<br><input type="checkbox" name="AccountInformation" value="AccountInformation"> AccountInformation
										{{-- <br> --}}
										<br><input type="checkbox" name="MerchantDiscount" value="MerchantDiscount"> MerchantDiscount
										{{-- <br> --}}
										<br><input type="checkbox" name="MerchantRateCard" value="MerchantRateCard"> MerchantRateCard
									</div>

								</div>

							</div>
							<div class="col-md-2">
								<button class="btn btn-success">Import Data</button>
							</div>
						</div>
					</form>
					<br>
					<p></p>
					<p> Bus Drop Off </p>
					<form action="{{ route('bd_import') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="row">
							<div class="col-md-8">
								<input type="file" name="file" class="form-control">
							</div>
							<div class="col-md-3">
								<button class="btn btn-success">Import Data</button>
							</div>
						</div>
					</form>
					<a class="btn btn-info mb-2" href="{{ route('send_noti') }}">send_noti</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@layout('templates.template')

@section('title')
	{{ Lang::line('extensions::extensions.title') }}
@endsection

@section('links')
	{{ Theme::asset('css/table.css') }}
@endsection

@section('body_scripts')
	{{ Theme::asset('extensions::js/extensions.js') }}
@endsection

@section('content')
<section id="extensions">

	<header class="head row">
		<div class="span6">
			<h1>{{ Lang::line('extensions::extensions.title') }}</h1>
			<p>{{ Lang::line('extensions::extensions.description') }}</p>
		</div>
	</header>

	<hr>

	<div class="tabbable">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#installed" data-toggle="tab">Installed</a></li>
			<li><a href="#uninstalled" data-toggle="tab">Uninstalled</a></li>
		</ul>
		<div class="tab-content">
		    <div class="tab-pane active" id="installed">

		    	<div class="row">
					<div class="span12">
						<div class="row">
							<ul id="table-filters-applied" class="nav nav-tabs span10"></ul>
						</div>
						<div class="row">
							<div class="span10">
								<div class="table-wrapper">
									<table id="installed-extension-table" class="table table-bordered">
										<thead>
											<tr>
												@foreach ($columns as $key => $col)
												<th data-table-key="{{ $key }}">{{ $col }}</th>
												@endforeach
												<th></th>
											</tr>
										<thead>
										<tbody>
											@include('extensions::partials.table_extensions')
										</tbody>
									</table>
								</div>
							</div>
							<div class="tabs-right span2">
								<div class="processing"></div>
								<ul id="table-pagination" class="nav nav-tabs"></ul>
							</div>
						</div>
					</div>
				</div>

		    </div>
		    <div class="tab-pane" id="uninstalled">

		    	<table id="uninstalled-extension-table" class="table table-bordered">
					<thead>
						<tr>
							<th>{{ Lang::line('extensions::extensions.name') }}</th>
							<th>{{ Lang::line('extensions::extensions.slug') }}</th>
							<th>{{ Lang::line('extensions::extensions.author') }}</th>
							<th>{{ Lang::line('extensions::extensions.description') }}</th>
							<th>{{ Lang::line('extensions::extensions.version') }}</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@forelse ($uninstalled as $extension)
							<tr>
								<td>
									{{ array_get($extension, 'info.name') }}
								</td>
								<td>
									{{ array_get($extension, 'info.slug') }}
								</td>
								<td>
									{{ array_get($extension, 'info.author') }}
								</td>
								<td>
									{{ array_get($extension, 'info.description') }}
								</td>
								<td>
									{{ array_get($extension, 'info.version') }}
								</td>
								<td>
									<a href="{{ url(ADMIN.'/extensions/install/'.array_get($extension, 'info.slug')) }}" onclick="return confirm('Are you sure you want to install the \'{{ e(array_get($extension, 'info.name')) }}\' extension?');">install</a>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6">
									Good news! All extensions have been installed!
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>

		    </div>
	  	</div>
	</div>

</section>

@endsection

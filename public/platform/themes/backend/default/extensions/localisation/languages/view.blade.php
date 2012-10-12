@layout('templates.default')

@section('title')
    {{ Lang::line('localisation::languages/general.title')->get() }}
@endsection

@section('content')
<section id="languages">
    <header class="head row-fluid">
        <div class="span6">
            <h1>{{ Lang::line('localisation::languages/general.title')->get() }}</h1>
            <p>{{ Lang::line('localisation::languages/general.description.view', array('language' => $language['name']))->get() }}</p>
        </div>
        <nav class="tertiary-navigation span6">
            @widget('platform.menus::menus.nav', 2, 1, 'nav nav-pills pull-right', ADMIN)
        </nav>
    </header>

    <hr />

    {{ Form::open() }}
        {{ Form::token() }}
        <fieldset>
            <div class="control-group">
                <label class="control-label" for="name">{{ Lang::line('localisation::languages/table.name')->get() }}</label>
                <div class="controls">
                    <input type="text" name="name" id="name" value="{{ Input::old('name', $language['name']); }}" placeholder="{{ Lang::line('localisation::languages/table.name')->get() }}" required>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="abbreviation">{{ Lang::line('localisation::languages/table.abbreviation')->get() }}</label>
                <div class="controls">
                    <input type="text" name="abbreviation" id="abbreviation" value="{{ Input::old('abbreviation', $language['abbreviation']); }}" placeholder="{{ Lang::line('localisation::languages/table.abbreviation')->get() }}" required>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="locale">{{ Lang::line('localisation::languages/table.locale')->get() }}</label>
                <div class="controls">
                    <input type="text" name="locale" id="locale" value="{{ Input::old('locale', $language['locale']); }}" placeholder="{{ Lang::line('localisation::languages/table.locale')->get() }}" required>
                    <span class="help-block"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="status">{{ Lang::line('localisation::languages/table.status')->get() }}</label>
                <div class="controls">
                    {{ Form::select('status', general_statuses(), $language['status']); }}
                    <span class="help-block"></span>
                </div>
            </div>
        </fieldset>

        <hr />

        <div class="form-actions">
            <a class="btn btn-large" href="{{ URL::to_admin('localisation/languages') }}">{{ Lang::line('button.cancel')->get() }}</a>
            <button class="btn btn-large btn-primary" type="submit" name="save" id="save" value="1">{{ Lang::line('button.update')->get() }}</button>
            <button class="btn btn-large btn-primary" type="submit" name="save_exit" id="save_exit" value="1">{{ Lang::line('button.update_exit')->get() }}</button>
            @if ( ! $language['default'])
            <a class="btn btn-large btn-danger" href="{{ URL::to_admin('localisation/languages/delete/' . $language['slug']) }}">{{ Lang::line('button.delete')->get() }}</a>
        	@endif
        </div>
    {{ Form::close() }}
</section>
@endsection
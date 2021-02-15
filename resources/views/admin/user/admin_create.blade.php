<div class="card">

    <div class="card-header">
        <div class="card-header-title">Add Admin</div>
    </div>

    <div class="card-body">

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="list-unstyled arrow">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        {!! Form::open(['route' => 'user.admin', 'method' => 'post']) !!}
        <div class="form-group">
            {!! Form::label('name', 'Name', ['class' => 'col-form-label']) !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'autocomplete' => 'name', 'required' => 'true', 'autofocus' => 'true']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('email', 'Email address', ['class' => 'col-form-label']) !!}
            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'name@example.com', 'autocomplete' => 'email', 'required' => 'true']) !!}
            <small class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group">
            {!! Form::label('password', 'Password', ['class' => 'col-form-label']) !!}
            {!! Form::password('password', ['class' => 'form-control', 'autocomplete' => 'new-password', 'require' => 'true']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-form-label']) !!}
            {!! Form::password('password_confirmation', ['class' => 'form-control', 'autocomplete' => 'new-password', 'required' => 'true']) !!}
        </div>
            <div class="form-group">
                <label for="sip_account" class="col-form-label">
                    Select Account<sup style="color: red">*</sup>
                    {{--(for more monitoring account email at <a href="mailto:fet@telecard.com.pk">fet@telecard.com.pk</a>)--}}
                </label>
                <select class="form-control" name="sip_account" id="sip_account" required>
                    @foreach($allowed_accounts as $key => $account)
                        <option value="{{ $account }}">{{ $account }}</option>
                    @endforeach
                </select>
            </div>
        {!! Form::button('<i class="far fa-edit"></i> Create', ['type' => 'submit', 'class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}
    </div>
</div>

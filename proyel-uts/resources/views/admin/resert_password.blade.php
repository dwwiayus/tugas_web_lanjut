<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body class="container" >
    <h1>Reset Password admin </h1>

    @if ($errors->any())
        @foreach ($errors->all() as $errors )
            <li>{{ $errors }}</li>
        @endforeach
    @endif

    @if (Session::has('error'))
        <li>{{Session::get('error') }}</li>
    @endif
    @if (Session::has('succes'))
        <li>{{Session::get('succes') }}</li>
    @endif

    <form action="{{route('admin.reset_password_submit')}}" method="POST" >
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">password</label>
          <input type="password" name="password" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
          <label for="exampleInputPassword1" class="form-label">Confirm Password</label>
          <input type="password" name="password_confirmation" class="form-control" id="exampleInputPassword1">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>
</body>
</html>

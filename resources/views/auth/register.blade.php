<!DOCTYPE html>
<html>
<body>
@if($errors->any())
    <div>{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ url('/register') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
    <label><input type="checkbox" name="accept_email"> Accept emails</label>
    <button type="submit">Register</button>
</form>
</body>
</html>

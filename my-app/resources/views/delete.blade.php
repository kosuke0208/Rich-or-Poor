<form action="{{ route('deletePlayer') }}" method="post">
@csrf
<p>
<input type="submit" value="削除">
</p>
</form>
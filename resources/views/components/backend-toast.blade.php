@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    toast.success('{{ session('success') }}');
});
</script>
@endif

@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    toast.error('{{ session('error') }}');
});
</script>
@endif

@if(session('warning'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    toast.warning('{{ session('warning') }}');
});
</script>
@endif

@if(session('info'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    toast.info('{{ session('info') }}');
});
</script>
@endif

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($errors->all() as $error)
        toast.error('{{ $error }}');
    @endforeach
});
</script>
@endif
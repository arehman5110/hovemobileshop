<form action="{{ route('jobs.store') }}" method="POST">
    @include('jobs.partials.form', [
        'job' => null,
        'buttonText' => 'Create Job'
    ])
</form>

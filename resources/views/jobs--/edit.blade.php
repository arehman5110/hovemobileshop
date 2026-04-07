<form action="{{ route('jobs.update', $job->id) }}" method="POST">
    @method('PUT')

    @include('jobs.partials.form', [
        'job' => $job,
        'buttonText' => 'Update Job'
    ])
</form>

<tbody>
    @forelse($projects as $project)
    <tr>
        <td>{{ $project->id }}</td>
        <td>{{ $project->name }}</td>
        <td>{{ $project->project_name }}</td>
        <td>{{ $project->email }}</td>
        <td>{{ $project->registered_at ? $project->registered_at->format('d.m.Y H:i') : '' }}</td>
        <td>
            <span class="badge bg-{{ $project->status === 'active' ? 'success' : 'secondary' }}">
                {{ $project->status === 'active' ? 'Активный' : 'Неактивный' }}
            </span>
        </td>
        <td>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-info" title="Просмотр" data-bs-toggle="modal" data-bs-target="#viewProjectModal{{ $project->id }}">
                    <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary" title="Редактировать" data-bs-toggle="modal" data-bs-target="#editProjectModal{{ $project->id }}">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-project" title="Удалить" data-id="{{ $project->id }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="text-center text-muted">Нет данных для отображения</td>
    </tr>
    @endforelse
</tbody> 
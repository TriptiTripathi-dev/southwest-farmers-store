<x-app-layout title="Newsletter Subscribers">
    <div class="content">
        <div class="container-fluid">
            <div class="py-4">
                <h4 class="h3 fw-bold m-0 ">Newsletter Subscribers</h4>
                <ol class="breadcrumb mt-2">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Newsletter Subscribers</li>
                </ol>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="mdi mdi-email-multiple-outline me-2 text-primary"></i>Subscribers List</h5>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#emailModal" onclick="prepareAllMail()">
                        <i class="mdi mdi-send me-1"></i> Send Email to All
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Email Address</th>
                                    <th>Store</th>
                                    <th>Subscribed At</th>
                                    <th class="text-end pr-4 pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscribers as $subscriber)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $subscriber->email }}</td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1 fw-semibold">
                                            {{ $subscriber->store->store_name ?? 'Headquarters' }}
                                        </span>
                                    </td>
                                    <td>{{ $subscriber->created_at->format('M d, Y H:i') }}</td>
                                    <td class="text-end pr-4 pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-sm btn-light border shadow-sm text-primary" title="Send Email" data-bs-toggle="modal" data-bs-target="#emailModal" onclick="prepareIndividualMail({{ $subscriber->id }}, '{{ $subscriber->email }}')">
                                                <i class="mdi mdi-email-outline fs-6"></i>
                                            </button>
                                            <form action="{{ route('settings.newsletter.destroy', $subscriber->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light border shadow-sm text-danger delete-btn" title="Remove Subscriber">
                                                    <i class="mdi mdi-trash-can fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No subscribers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($subscribers->hasPages())
                        <div class="card-footer bg-white border-top py-3">
                            {{ $subscribers->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form action="{{ route('settings.newsletter.send-mail') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="subscriber_id" id="modalSubscriberId">
                    <div class="modal-header border-bottom bg-light py-3 px-4 rounded-top-4">
                        <h5 class="modal-title fw-bold text-dark" id="emailModalLabel">Send Newsletter Email</h5>
                        <button type="button" class="btn-close" data-bs-redirect="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Recipient</label>
                            <input type="text" id="modalRecipient" class="form-control bg-light border-0" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Subject <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control bg-light border-0" placeholder="e.g. Weekly Fresh Deals & Updates" required>
                            <div class="invalid-feedback">Please enter a subject.</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold text-muted small">Email Message (HTML allowed) <span class="text-danger">*</span></label>
                            <textarea name="body" id="mailEditor" class="form-control bg-light border-0 editor" rows="8" placeholder="Type your newsletter message here..." required></textarea>
                            <div class="invalid-feedback">Please enter message body.</div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light py-3 px-4 rounded-bottom-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="mdi mdi-send me-1"></i> Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <script>
        let ckEditorInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('mailEditor')) {
                ckEditorInstance = CKEDITOR.replace('mailEditor', {
                    removeButtons: 'Source,Save,NewPage,ExportPdf,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CopyFormatting,RemoveFormat,Subscript,Superscript,Outdent,Indent,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Link,Unlink,Anchor,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Styles,Format,Font,FontSize,TextColor,BGColor,Maximize,ShowBlocks,About'
                });
            }

            // Sync CKEditor data back to textarea before submit validation
            const form = document.querySelector('#emailModal form');
            if (form) {
                form.addEventListener('submit', function() {
                    if (ckEditorInstance) {
                        document.getElementById('mailEditor').value = ckEditorInstance.getData();
                    }
                });
            }
        });

        function prepareAllMail() {
            document.getElementById('emailModalLabel').innerText = 'Send Email to All Subscribers';
            document.getElementById('modalSubscriberId').value = '';
            document.getElementById('modalRecipient').value = 'All Subscribers';
            if (ckEditorInstance) {
                ckEditorInstance.setData('');
            } else {
                document.getElementById('mailEditor').value = '';
            }
            document.querySelector('#emailModal form').classList.remove('was-validated');
        }

        function prepareIndividualMail(id, email) {
            document.getElementById('emailModalLabel').innerText = 'Send Email to Individual Subscriber';
            document.getElementById('modalSubscriberId').value = id;
            document.getElementById('modalRecipient').value = email;
            if (ckEditorInstance) {
                ckEditorInstance.setData('');
            } else {
                document.getElementById('mailEditor').value = '';
            }
            document.querySelector('#emailModal form').classList.remove('was-validated');
        }
    </script>
    @endpush
</x-app-layout>

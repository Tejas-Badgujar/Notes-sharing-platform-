<?php 
$level = '../'; 
$page_title = 'Upload Note';
include '../includes/config.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../auth/login.php'); exit(); }
$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
include '../includes/header.php'; 
?>
<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>
    <main class="dashboard-content">
        <h2 class="animate-fade-in-up" style="margin-bottom: 2rem;">Upload New Note</h2>

        <?php if($success): ?>
        <div style="background:rgba(0,255,136,0.1);border:1px solid rgba(0,255,136,0.4);color:#00ff88;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.5rem;font-size:0.9rem;">
            <i class="fa-solid fa-circle-check"></i> Note uploaded successfully! It will be reviewed by admins.
        </div>
        <?php endif; ?>

        <?php if($error): ?>
        <div style="background:rgba(255,51,102,0.1);border:1px solid rgba(255,51,102,0.4);color:#ff6688;padding:0.8rem 1rem;border-radius:10px;margin-bottom:1.5rem;font-size:0.9rem;">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= $error==='size' ? 'File too large (max 500MB).' : ($error==='type' ? 'Only PDF files are allowed.' : 'Please fill all required fields and upload a valid PDF.') ?>
        </div>
        <?php endif; ?>

        <div class="glass-panel animate-fade-in-up delay-100" style="padding: 3rem; max-width: 800px; margin: 0 auto;">
            <form action="process_upload.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" id="title" name="title" class="form-control" placeholder=" " required>
                    <label for="title" class="form-label">Note Title</label>
                </div>
                <div class="form-group">
                    <select name="field" class="form-control" style="color: var(--text-secondary); appearance: none;" required>
                        <option value="" disabled selected>Select Field</option>
                        <option value="IT">Information Technology</option>
                        <option value="CS">Computer Science</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Mechanical">Mechanical</option>
                        <option value="Civil">Civil Engineering</option>
                        <option value="IPS">IPS</option>
                        <option value="UPSC">UPSC</option>
                        <option value="MPSC">MPSC</option>
                        <option value="General">General</option>
                    </select>
                    <i class="fa-solid fa-chevron-down" style="position: absolute; right: 1.2rem; top: 1.2rem; color: var(--text-secondary); pointer-events: none;"></i>
                </div>
                <div class="form-group">
                    <input type="text" id="subject" name="subject" class="form-control" placeholder=" " required>
                    <label for="subject" class="form-label">Subject Name</label>
                </div>
                <div class="form-group">
                    <textarea id="description" name="description" class="form-control" placeholder=" " rows="3" style="resize:vertical;min-height:80px;"></textarea>
                    <label for="description" class="form-label">Description (optional)</label>
                </div>
                <div class="form-group">
                    <input type="text" id="tags" name="tags" class="form-control" placeholder=" ">
                    <label for="tags" class="form-label">Tags (comma separated)</label>
                </div>
                <div id="upload-drop-zone" style="border:2px dashed rgba(0,243,255,0.3);border-radius:16px;padding:4rem 2rem;text-align:center;margin-bottom:2rem;background:rgba(0,243,255,0.02);position:relative;overflow:hidden;transition:all 0.3s;">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size:3rem;color:var(--accent-cyan);margin-bottom:1rem;"></i>
                    <h3 style="font-size:1.2rem;margin-bottom:0.5rem;">Drag & Drop your PDF here</h3>
                    <p style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:1rem;">Max 500MB &bull; PDF only</p>
                    <p id="file-name" style="color:var(--accent-cyan);font-weight:600;display:none;margin-bottom:1rem;"></p>
                    <input type="file" id="file" name="file" accept=".pdf" style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer;" required>
                    <div class="btn btn-outline" style="pointer-events:none;">Browse Files</div>
                </div>
                <div style="text-align:right;">
                    <button type="submit" class="btn btn-primary magnetic-btn" style="padding:1rem 2.5rem;font-size:1.1rem;">Submit Note <i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    </main>
</div>
<script>
document.getElementById('file').addEventListener('change',function(){
    const n=this.files[0]?.name;
    if(n){document.getElementById('file-name').textContent='📄 '+n;document.getElementById('file-name').style.display='block';}
});
// Drag & drop visual feedback
const dz = document.getElementById('upload-drop-zone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.style.borderColor='var(--accent-cyan)'; dz.style.background='rgba(0,243,255,0.06)'; });
dz.addEventListener('dragleave', () => { dz.style.borderColor='rgba(0,243,255,0.3)'; dz.style.background='rgba(0,243,255,0.02)'; });
dz.addEventListener('drop', () => { dz.style.borderColor='rgba(0,243,255,0.3)'; dz.style.background='rgba(0,243,255,0.02)'; });
</script>
<?php include '../includes/footer.php'; ?>

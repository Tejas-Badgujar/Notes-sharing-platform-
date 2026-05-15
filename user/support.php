<?php 
$level = '../'; 
$page_title = 'Report a Problem';
include '../includes/header.php'; 
?>

<div class="dashboard-layout">
    <?php include '../includes/sidebar.php'; ?>

    <main class="dashboard-content">
        <div style="margin-bottom: 2.5rem;" class="animate-fade-in-up">
            <h2>Support & Feedback</h2>
            <p style="color: var(--text-secondary); margin-top: 0.5rem;">Need help? Connect with our development team directly.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
            
            <!-- Developer 1: Tejas Badgujar -->
            <div class="glass-panel animate-fade-in-up delay-100" style="padding: 2.5rem; border-radius: 24px; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: var(--accent-cyan); filter: blur(100px); opacity: 0.1;"></div>
                
                <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, var(--accent-cyan), var(--accent-violet)); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; color: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">TB</div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem;">Tejas Badgujar</h3>
                        <p style="margin: 5px 0 0; color: var(--accent-cyan); font-weight: 500;">Lead Full-Stack Developer</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="https://www.linkedin.com/in/tejas-badgujar-b4607a339/" target="_blank" class="btn btn-primary" style="background: #0a66c2; box-shadow: 0 4px 15px rgba(10,102,194,0.3); border: none;">
                        <i class="fa-brands fa-linkedin"></i> Message on LinkedIn
                    </a>
                    <a href="https://wa.me/917559493061" target="_blank" class="btn btn-outline" style="border-color: #25d366; color: #25d366; background: rgba(37, 211, 102, 0.05);">
                        <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>

            <!-- Developer 2: Bhagyesh Patil -->
            <div class="glass-panel animate-fade-in-up delay-200" style="padding: 2.5rem; border-radius: 24px; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: var(--accent-magenta); filter: blur(100px); opacity: 0.1;"></div>
                
                <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, var(--accent-violet), var(--accent-magenta)); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold; color: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.2);">BP</div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.5rem;">Bhagyesh Patil</h3>
                        <p style="margin: 5px 0 0; color: var(--accent-magenta); font-weight: 500;">Backend & UI Architect</p>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="https://www.linkedin.com/in/bhagyeshrmy4143/" target="_blank" class="btn btn-primary" style="background: #0a66c2; box-shadow: 0 4px 15px rgba(10,102,194,0.3); border: none;">
                        <i class="fa-brands fa-linkedin"></i> Message on LinkedIn
                    </a>
                    <a href="https://wa.me/918459406802" target="_blank" class="btn btn-outline" style="border-color: #25d366; color: #25d366; background: rgba(37, 211, 102, 0.05);">
                        <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>

        </div>

        <!-- Feedback Form -->
        <div class="glass-panel animate-fade-in-up delay-300" style="margin-top: 3rem; padding: 2.5rem;">
            <h3 style="margin-bottom: 1.5rem;">Send a Quick Report</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder=" " id="issue-subject">
                    <label for="issue-subject" class="form-label">Issue Subject</label>
                </div>
                <div class="form-group">
                    <select class="form-control" style="cursor: pointer;">
                        <option>Bug Report</option>
                        <option>Feature Request</option>
                        <option>Account Issue</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <textarea class="form-control" placeholder=" " style="min-height: 120px;"></textarea>
                <label class="form-label">Describe the problem in detail...</label>
            </div>
            <button class="btn btn-primary" style="padding: 1rem 2.5rem;">Submit Report</button>
        </div>

    </main>
</div>

<?php include '../includes/footer.php'; ?>

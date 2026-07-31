<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

// Contact form handling
$formSent = false;
$formError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $subject === '' || $message === '') {
        $formError = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formError = 'Please enter a valid email address.';
    } else {
        try {
            execute_query(
                'INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)',
                'ssss',
                [$name, $email, $subject, $message]
            );
            $formSent = true;
        } catch (Exception $ex) {
            $formError = 'Could not send your message. Please try again later.';
        }
    }
}

// Data from database
$projects = fetch_all('SELECT * FROM projects ORDER BY id DESC');
$services = fetch_all('SELECT * FROM services ORDER BY sort_order ASC, id ASC');
$skills   = fetch_all('SELECT * FROM skills ORDER BY category ASC, percent DESC');

$skillCategories = [];
foreach ($skills as $skill) {
    $skillCategories[$skill['category']][] = $skill;
}

$projectCategories = ['All'];
foreach ($projects as $project) {
    $projectCategories[$project['category']] = $project['category'];
}
?>

<main>

  <!-- ===================== HERO ===================== -->
  <section class="hero d-flex align-items-center">
    <div class="hero-bg"></div>
    <div class="container position-relative">
      <div class="row align-items-center">
        <div class="col-lg-7 hero-content">
          <span class="hero-greeting"><?php echo e(get_setting('hero_greeting', 'Hello, I am')); ?></span>
          <h1 class="hero-name"><?php echo e(get_setting('hero_name', 'Alex Carter')); ?></h1>
          <div class="hero-role">
            <span class="typing" data-words='["Web Developer", "PHP & MySQL Specialist", "Angular.js Developer", "WordPress Expert", "Bootstrap UI Designer"]'><?php echo e(get_setting('hero_role', 'Web Developer')); ?></span>
          </div>
          <p class="hero-tagline"><?php echo e(get_setting('hero_tagline', '')); ?></p>
          <div class="d-flex flex-wrap gap-3 mt-4">
            <a href="#projects" class="btn btn-grad btn-lg px-4"><?php echo e(get_setting('hero_btn_text', 'View My Work')); ?></a>
            <a href="#contact" class="btn btn-outline-light btn-lg px-4">Contact Me</a>
          </div>
          <div class="hero-socials mt-4">
            <a href="<?php echo e(get_setting('social_github', '#')); ?>" target="_blank" rel="noopener" aria-label="GitHub"><i class="bi bi-github"></i></a>
            <a href="<?php echo e(get_setting('social_linkedin', '#')); ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
            <a href="<?php echo e(get_setting('social_twitter', '#')); ?>" target="_blank" rel="noopener" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="mailto:<?php echo e(get_setting('email', '')); ?>" aria-label="Email"><i class="bi bi-envelope"></i></a>
          </div>
        </div>
        <div class="col-lg-5 text-center hero-visual">
          <div class="code-window">
            <div class="code-window-bar">
              <span></span><span></span><span></span>
              <small>portfolio.js</small>
            </div>
            <pre class="code-window-body"><code><span class="c-kw">const</span> dev = {<br>  <span class="c-key">name</span>: <span class="c-str">"<?php echo e(get_setting('hero_name', 'Alex Carter')); ?>"</span>,<br>  <span class="c-key">stack</span>: [<span class="c-str">"HTML"</span>, <span class="c-str">"CSS"</span>, <span class="c-str">"JS"</span>,<br>           <span class="c-str">"PHP"</span>, <span class="c-str">"MySQL"</span>, <span class="c-str">"TS"</span>],<br>  <span class="c-key">tools</span>: [<span class="c-str">"Angular"</span>, <span class="c-str">"Bootstrap"</span>,<br>           <span class="c-str">"WordPress"</span>, <span class="c-str">"Mobirise"</span>],<br>  <span class="c-kw">async</span> <span class="c-fn">build</span>() { <br>    <span class="c-kw">return</span> <span class="c-str">"Awesome Web"</span>;<br>  }<br>};</code></pre>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== ABOUT ===================== -->
  <section id="about" class="section">
    <div class="container">
      <div class="section-head text-center mb-5">
        <span class="section-tag">About</span>
        <h2 class="section-title"><?php echo e(get_setting('about_title', 'About Me')); ?></h2>
      </div>
      <div class="row align-items-center g-5">
        <div class="col-lg-5 text-center">
          <div class="about-card">
            <?php $aboutPhoto = get_setting('about_photo', ''); ?>
            <?php if ($aboutPhoto !== ''): ?>
              <img src="<?php echo SITE_URL . '/admin/uploads/' . e($aboutPhoto); ?>" alt="<?php echo e(get_setting('hero_name', 'Developer')); ?>" class="about-photo">
            <?php else: ?>
              <div class="about-photo placeholder-photo">
                <i class="bi bi-person-fill"></i>
              </div>
            <?php endif; ?>
            <h4 class="mt-4 mb-1"><?php echo e(get_setting('hero_name', 'Alex Carter')); ?></h4>
            <p class="text-muted"><?php echo e(get_setting('hero_role', 'Web Developer')); ?></p>
          </div>
        </div>
        <div class="col-lg-7">
          <p class="about-text lead"><?php echo e(get_setting('about_text', '')); ?></p>
          <div class="row g-4 mt-2 about-info">
            <div class="col-sm-6">
              <div class="info-item"><i class="bi bi-envelope-fill"></i><div><small>Email</small><strong><?php echo e(get_setting('email', '')); ?></strong></div></div>
            </div>
            <div class="col-sm-6">
              <div class="info-item"><i class="bi bi-telephone-fill"></i><div><small>Phone</small><strong><?php echo e(get_setting('phone', '')); ?></strong></div></div>
            </div>
            <div class="col-sm-6">
              <div class="info-item"><i class="bi bi-geo-alt-fill"></i><div><small>Location</small><strong><?php echo e(get_setting('location', '')); ?></strong></div></div>
            </div>
            <div class="col-sm-6">
              <div class="info-item"><i class="bi bi-laptop-fill"></i><div><small>Freelance</small><strong>Available</strong></div></div>
            </div>
          </div>
          <div class="mt-4">
            <a href="#contact" class="btn btn-grad px-4">Let's Talk <i class="bi bi-arrow-right ms-1"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== SKILLS ===================== -->
  <section id="skills" class="section section-alt">
    <div class="container">
      <div class="section-head text-center mb-5">
        <span class="section-tag">Skills</span>
        <h2 class="section-title">My Tech Stack</h2>
      </div>
      <?php foreach ($skillCategories as $category => $catSkills): ?>
        <h5 class="skill-category"><i class="bi bi-tag-fill me-2"></i><?php echo e($category); ?></h5>
        <div class="row g-4 mb-4">
          <?php foreach ($catSkills as $skill): ?>
            <div class="col-md-6 col-lg-4">
              <div class="skill-box">
                <div class="d-flex justify-content-between mb-2">
                  <strong><?php echo e($skill['name']); ?></strong>
                  <span class="skill-percent"><?php echo (int)$skill['percent']; ?>%</span>
                </div>
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width: 0" data-width="<?php echo (int)$skill['percent']; ?>%" aria-valuenow="<?php echo (int)$skill['percent']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ===================== SERVICES ===================== -->
  <section id="services" class="section">
    <div class="container">
      <div class="section-head text-center mb-5">
        <span class="section-tag">Services</span>
        <h2 class="section-title">What I Do</h2>
      </div>
      <div class="row g-4">
        <?php foreach ($services as $service): ?>
          <div class="col-md-6 col-lg-3">
            <div class="service-card h-100">
              <div class="service-icon"><i class="bi <?php echo e($service['icon']); ?>"></i></div>
              <h5 class="mt-3"><?php echo e($service['title']); ?></h5>
              <p class="text-muted small mb-0"><?php echo e($service['description']); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== RESUME ===================== -->
  <section id="resume" class="section section-alt">
    <div class="container">
      <div class="section-head text-center mb-5">
        <span class="section-tag">Resume</span>
        <h2 class="section-title">Experience & Education</h2>
      </div>
      <p class="text-center text-muted mb-5"><?php echo e(get_setting('resume_text', '')); ?></p>
      <div class="row g-5">
        <div class="col-lg-6">
          <h4 class="resume-head"><i class="bi bi-briefcase-fill me-2"></i>Experience</h4>
          <div class="timeline">
            <?php if (get_setting('experience_1_year', '') !== ''): ?>
              <div class="timeline-item">
                <span class="timeline-dot"></span>
                <div class="timeline-year"><?php echo e(get_setting('experience_1_year', '')); ?></div>
                <h5><?php echo e(get_setting('experience_1_role', '')); ?></h5>
                <p class="text-muted mb-0"><?php echo e(get_setting('experience_1_text', '')); ?></p>
              </div>
            <?php endif; ?>
            <?php if (get_setting('experience_2_year', '') !== ''): ?>
              <div class="timeline-item">
                <span class="timeline-dot"></span>
                <div class="timeline-year"><?php echo e(get_setting('experience_2_year', '')); ?></div>
                <h5><?php echo e(get_setting('experience_2_role', '')); ?></h5>
                <p class="text-muted mb-0"><?php echo e(get_setting('experience_2_text', '')); ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="col-lg-6">
          <h4 class="resume-head"><i class="bi bi-mortarboard-fill me-2"></i>Education</h4>
          <div class="timeline">
            <?php if (get_setting('education_1_year', '') !== ''): ?>
              <div class="timeline-item">
                <span class="timeline-dot"></span>
                <div class="timeline-year"><?php echo e(get_setting('education_1_year', '')); ?></div>
                <h5><?php echo e(get_setting('education_1_role', '')); ?></h5>
                <p class="text-muted mb-0"><?php echo e(get_setting('education_1_text', '')); ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===================== PROJECTS ===================== -->
  <section id="projects" class="section">
    <div class="container">
      <div class="section-head text-center mb-5">
        <span class="section-tag">Portfolio</span>
        <h2 class="section-title">Featured Projects</h2>
      </div>

      <div class="d-flex flex-wrap justify-content-center gap-2 mb-5 project-filters">
        <?php $first = true; ?>
        <?php foreach ($projectCategories as $cat): ?>
          <button class="btn filter-btn <?php echo $first ? 'active' : ''; ?>" data-filter="<?php echo $first ? 'all' : e($cat); ?>"><?php echo $first ? 'All' : e($cat); ?></button>
          <?php $first = false; ?>
        <?php endforeach; ?>
      </div>

      <div class="row g-4" id="projectGrid">
        <?php foreach ($projects as $project): ?>
          <div class="col-md-6 col-lg-4 project-item" data-category="<?php echo e($project['category']); ?>">
            <div class="project-card">
              <div class="project-thumb">
                <?php if ($project['image']): ?>
                  <img src="<?php echo SITE_URL . '/admin/uploads/' . e($project['image']); ?>" alt="<?php echo e($project['title']); ?>">
                <?php else: ?>
                  <div class="project-thumb-ph"><i class="bi bi-code-slash"></i></div>
                <?php endif; ?>
                <div class="project-overlay">
                  <?php if ($project['url']): ?>
                    <a href="<?php echo e($project['url']); ?>" target="_blank" rel="noopener" class="btn btn-light btn-sm">Live Demo <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                  <?php endif; ?>
                </div>
              </div>
              <div class="project-body">
                <span class="badge bg-accent mb-2"><?php echo e($project['category']); ?></span>
                <h5><?php echo e($project['title']); ?></h5>
                <p class="text-muted small mb-2"><?php echo e($project['description']); ?></p>
                <small class="tech-stack"><i class="bi bi-cpu me-1"></i><?php echo e($project['tech_stack']); ?></small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ===================== CONTACT ===================== -->
  <section id="contact" class="section section-alt">
    <div class="container">
      <div class="section-head text-center mb-5">
        <span class="section-tag">Contact</span>
        <h2 class="section-title"><?php echo e(get_setting('contact_title', 'Contact Me')); ?></h2>
        <p class="text-muted"><?php echo e(get_setting('contact_subtitle', '')); ?></p>
      </div>
      <div class="row g-5">
        <div class="col-lg-5">
          <div class="contact-info-card p-4">
            <div class="contact-info-item">
              <i class="bi bi-envelope-fill"></i>
              <div><small>Email</small><strong><?php echo e(get_setting('email', '')); ?></strong></div>
            </div>
            <div class="contact-info-item">
              <i class="bi bi-telephone-fill"></i>
              <div><small>Phone</small><strong><?php echo e(get_setting('phone', '')); ?></strong></div>
            </div>
            <div class="contact-info-item">
              <i class="bi bi-geo-alt-fill"></i>
              <div><small>Location</small><strong><?php echo e(get_setting('location', '')); ?></strong></div>
            </div>
            <hr>
            <div class="contact-socials">
              <a href="<?php echo e(get_setting('social_github', '#')); ?>" target="_blank" rel="noopener" aria-label="GitHub"><i class="bi bi-github"></i></a>
              <a href="<?php echo e(get_setting('social_linkedin', '#')); ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
              <a href="<?php echo e(get_setting('social_twitter', '#')); ?>" target="_blank" rel="noopener" aria-label="Twitter"><i class="bi bi-twitter-x"></i></a>
            </div>
          </div>
        </div>
        <div class="col-lg-7">
          <?php if ($formSent): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>Your message has been sent! I will reply soon.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php elseif ($formError !== ''): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($formError); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <form method="post" class="contact-form" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cName" name="name" placeholder="Your Name" required>
                  <label for="cName">Your Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="email" class="form-control" id="cEmail" name="email" placeholder="Your Email" required>
                  <label for="cEmail">Your Email</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating">
                  <input type="text" class="form-control" id="cSubject" name="subject" placeholder="Subject" required>
                  <label for="cSubject">Subject</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-floating">
                  <textarea class="form-control" id="cMessage" name="message" placeholder="Message" style="height: 140px" required></textarea>
                  <label for="cMessage">Message</label>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" name="contact_submit" class="btn btn-grad btn-lg px-5">Send Message <i class="bi bi-send ms-2"></i></button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

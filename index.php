<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="https://github.com/sam38">
    <link rel="icon" href="<?php get_template_directory_uri() . '/assets/images/favicon.png'; ?>">
    <title>Tower Forms</title>
    <?php wp_head(); ?>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="/">
            <img src="<?php echo get_template_directory_uri() . '/assets/images/favicon.png'; ?>" alt="Tower"> Tower
        </a>
    </nav>

    <main role="main" class="container offset-nav pt-5 mb-5">
        <div class="row">
            <div class="col-md-3">
                <div 
                    class="nav flex-md-column nav-pills" 
                    id="v-pills-tab" 
                    role="tablist" 
                    aria-orientation="vertical"
                >
                    <a 
                        id="btn-form-policy" 
                        class="nav-link active" 
                        data-toggle="pill" 
                        href="#tab-policy" 
                        role="tab" 
                        aria-controls="tab-policy" 
                        aria-selected="true"
                    >Insurance Policy</a>
                    <a 
                        id="btn-form-claim" 
                        class="nav-link" 
                        data-toggle="pill" 
                        data-lazyload="form-claim"
                        href="#tab-claim" 
                        role="tab" 
                        aria-controls="tab-claim" 
                        aria-selected="false"
                    >Insurance Policy Claim</a>
                </div>
            </div><!-- /.col-3 -->
            <div class="col mt-5 mt-md-0">
                <div class="tab-content" id="v-pills-tabContent">
                    <div 
                        class="tab-pane fade show active" 
                        id="tab-policy" 
                        role="tabpanel" 
                        aria-labelledby="btn-form-policy"
                    >
                        <?php get_template_part('template-parts/form', 'policy'); ?>
                    </div><!-- /.tab-pane[0] -->
                    <div 
                        class="tab-pane fade" 
                        id="tab-claim" 
                        role="tabpanel" 
                        aria-labelledby="btn-form-claim"
                    >
                        <i class="fa fa-spinner spin" aria-hidden="true"></i> Loading...
                        <?php /* get_template_part('template-parts/form', 'claim');*/ ?>
                    </div><!-- /.tab-pane[1] -->
                </div><!-- /.tab-content -->
                    
            </div><!-- /.col -->
        </div><!-- /.row -->
    </main><!-- /.container -->

    <footer class="mb-3 text-center text-muted">
        <small>By <a href="https://github.com/sam38/tower" target="_blank">Sudarshan Shakya</a></small>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
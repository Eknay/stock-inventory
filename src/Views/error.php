<?php

declare(strict_types=1);

/** @var int $statusCode */
/** @var string $message */
/** @var bool $debug */
/** @var string $details */
?>
<section class="error-page">
    <div class="error-code"><?= e($statusCode) ?></div>
    <h1>Bir şeyler ters gitti.</h1>
    <p><?= e($message) ?></p>
    <?php if ($debug && ($details ?? '') !== ''): ?>
        <pre class="error-details"><?= e($details) ?></pre>
    <?php endif; ?>
    <a class="button button-primary" href="/">Dashboard'a dön</a>
</section>

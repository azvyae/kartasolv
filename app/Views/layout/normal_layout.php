<main class="my-auto">
    <?= $this->renderSection('main'); ?>
</main>
<?php if (!isSameController(['Test'])) : ?>
    <?= $this->include('layout/footer'); ?>
<?php endif ?>
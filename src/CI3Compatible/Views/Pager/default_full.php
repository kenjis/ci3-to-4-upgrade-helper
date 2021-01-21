<?php

/**
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */

$pager->setSurroundCount(2);
?>

<?php if ($pager->hasPreviousPage()) : ?>
    <a href="<?= $pager->getFirst() ?>" data-ci-pagination-page="<?= $pager->first ?> rel="start">&lsaquo; <?= lang('Pager.first') ?></a>

    <a href="<?= $pager->getPreviousPage() ?>" data-ci-pagination-page="<?= $pager->current - 1 ?>" rel="prev">&lt;</a>
<?php endif ?>

<?php foreach ($pager->links() as $link) : ?>
    <?php if ($link['active']) : ?>
        <strong><?= $link['title'] ?></strong>
    <?php else : ?>
        <a href="<?= $link['uri'] ?>" data-ci-pagination-page="<?= $link['title'] ?>">
            <?= $link['title'] ?>
        </a>
    <?php endif ?>
<?php endforeach ?>

<?php if ($pager->hasNextPage()) : ?>
    <a href="<?= $pager->getNextPage() ?>" data-ci-pagination-page="<?= $pager->current + 1 ?>" rel="next">&gt;</a>

    <a href="<?= $pager->getLast() ?>" data-ci-pagination-page="<?= $pager->last ?>"><?= lang('Pager.last') ?> &rsaquo;</a>
<?php endif ?>

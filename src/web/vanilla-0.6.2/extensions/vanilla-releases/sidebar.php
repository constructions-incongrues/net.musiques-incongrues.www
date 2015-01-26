<?php
$releasesPeer = new ReleasesPage($Context, $Configuration);
$discussion = $releasesPeer->getDiscussion(ForceIncomingInt('DiscussionID', null));
$releases = array();
if (false !== $discussion && $discussion['LabelName']) {
    $releases = $releasesPeer->getReleases($discussion['LabelName']);
    if (count($releases) > 6) {
        $limit = 6;
    } else {
        $limit = count($releases);
    }
}
?>
<?php if (count($releases) > 1): ?>
<h2>Et aussi</h2>
<ul class="ailleurs-links">
    <?php for ($i = 0; $i < $limit; $i++): ?>
        <?php if ($releases[$i]['DiscussionID'] != ForceIncomingInt('DiscussionID', null)): ?>
    <li>
        <a href="<?php echo GetUrl($Context->Configuration, 'comments.php', '', 'DiscussionID', $releases[$i]['DiscussionID'], '', '#Item_1', CleanupString($releases[$i]['Name']).'/') ?>"><?php echo $releases[$i]['Name'] ?></a>
    </li>
        <?php endif; ?>
    <?php endfor; ?>
</ul>
<p style="text-align:center;margin-top:1em;">
    <a href="<?php echo sprintf('%sreleases/?label=%s', $Configuration['WEB_ROOT'], urlencode($discussion['LabelName'])) ?>" title="Voir toutes les sorties">
        Voir les <?php echo count($releases) ?> sortie(s) de <br /><?php echo $discussion['LabelName'] ?>
    </a>
</p>
<?php endif; ?>

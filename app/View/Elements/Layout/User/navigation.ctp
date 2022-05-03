<?php 
    $userID = !empty($user['User']['id']) ? $user['User']['id'] : Hash::get($this->data, 'User.id');
    $userID = !empty($partnerUserId) ? $partnerUserId : $userID;
    if (!empty($userID)) :
?>
<nav class="navbar navbar-default navbar-fixed-side panel panel-primary">
    <div class="panel-heading">User Pages</div>
    <table>
        <tr>
            <td>
                <?php if ($this->Session->read('Auth.User.id') === $userID) : ?>
                <div>
                    <a href="/Users/secret">
                        Two-Factor Authentication</a>
                </div>
                <?php endif; ?> 
                <div>
                    <a href="/Adjustments/index/<?php echo $userID; ?>/sort:Adjustment.adj_date/direction:desc">
                        Rep Adjustments</a>
                </div>
                <div>
                    <a href="/Orders/index">
                        Equipment Orders</a>
                </div>
            </td>
        </tr>
    </table>
</nav>
<?php endif; ?>
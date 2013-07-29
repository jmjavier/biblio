<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Biblio
 * @author    Miguel Javier <miguel.javier@gmail.com>
 * @license   GPL-3.0
 * @link      http://javijavier.com
 * @copyright 2013 Miguel Javier
  */

	if (isset($_POST['biblio_key'])){
		update_option( 'biblio_key', $_POST['biblio_key'] );
	}
	$bkey = get_option('biblio_key');
?>
<div class="wrap">
	<h2>Biblio</h2>
	<p>Biblio lets you save books from Amazon using a bookmarklet.</p>
	<form name="biblio_key_form" method="post" action="">
	<input name="biblio_key" type="text" value="<?php echo $bkey; ?>"/>
	<input type="submit" value="Save"/>
	</form>
	<p><a id="biblio_new_key" href="#">Create new key</a></p>
	Drag this bookmarklet to your bookmarks bar:
	<a id="bookmarklet" href="javascript:var%20d%3Ddocument%2Casin%3Ddocument.getElementById(%27ASIN%27)%7C%7Cdocument.getElementsByName(%27ASIN.0%27)%5B0%5D%3Bif(!d.body)%7Balert(%22Please%20wait%20until%20the%20page%20has%20loaded.%22)%7Delse%20if(!asin)%7Balert(%22Please%20navigate%20to%20an%20item%20page%20on%20Amazon.%22)%7Delse%7Bvar%20i%3Ddocument.createElement(%22iframe%22)%3Bi.setAttribute(%22name%22%2C%22biblio12345%22)%3Bi.setAttribute(%22id%22%2C%22biblio12345%22)%3Bi.setAttribute(%22allowtransparency%22%2C%22true%22)%3Bi.setAttribute(%22style%22%2C%22border%3A%200%3B%20width%3A%201px%3B%20height%3A%201px%3B%20position%3A%20absolute%3B%20left%3A%200%3B%20top%3A%200%3B%22)%3Bi.setAttribute(%22src%22%2C%22<?php urlencode(bloginfo('url'));?>%3Fbibliokey%3D<?php echo $bkey; ?>%26asin%3D%22%2Basin.value)%3Bi.setAttribute(%22onload%22%2C%22javascript%3Aalert(%27Saved!%27)%3B%22)%3Bdocument.body.appendChild(i)%7D"/>Book It</a>
</div>

<script>
jQuery('#biblio_new_key').click(function(){
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    for( var i=0; i < 12; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    jQuery('input[name="biblio_key"]').val(text);

})
</script>
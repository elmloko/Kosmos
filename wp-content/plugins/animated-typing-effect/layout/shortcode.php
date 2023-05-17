<div class="wrap">
<h2>Typing Effect shortcode generator</h2>

<div id="poststuff">
  <div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content" style="position: relative;">

      <div id="titlediv">
        <div id="titlewrap" class="type-strings">
          <div class="shortcode">
            <div class="">Copy and paste the shortcode generated below into a page, post or widget.</div>
            <div id="typed"></div>
            <div class="">If you want to use the shortcode directly in a theme or template file, copy and paste the code below.</div>
            <div id="php"></div>
          </div>

          <h2>Enter each 'string' or sentence that you want to be typed out into a row below. Click the 'Add Row' button to add additional sentences.</h2>
          <div class="strings">
          	<input type="text" name="string[]" size="30" value="First sentence" id="title" spellcheck="true" autocomplete="off" placeholder="Enter title here">
          	<input type="text" name="string[]" size="30" value="Second sentence" id="title" spellcheck="true" autocomplete="off" placeholder="Enter title here">
            <label type="button" name="remove-string" class="button button-large">Remove</label>
          </div>

          <div class="controls">
            <input type="button" name="add-string" value="Add Row" class="add button button-primary button-large">
          </div>
        </div>
      </div>
    </div>

    <div id="postbox-container-1" class="postbox-container">
      <div id="side-sortables" class="meta-box-sortables ui-sortable" style=""><div id="slide" >
        <?php do_meta_boxes( 'typed_metaboxes', 'advanced', null ); ?>
      </div>
    </div>
  </div>
</div>

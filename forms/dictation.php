<div class='dictation-panel'>
	<div>
		<a target='_blank' href='http://dictation.gearinvent.com'>
			<strong>DICTATION</strong>
		</a>
		<?php echo Dictation::get_support_links(); ?>
	</div>
	<hr>
	<div>
	<?php 
	echo '<img src="'.Dictation::$baseurl.'/images/mic.jpg" alt="MIC" width="80" style="margin-top: 20px;margin-left: 40px;position: relative;" />'; 
	?>
    <input style="-webkit-transform: scale(6,6);opacity: .001;width: 30px;border: none;position: absolute;top: 85px;left: 65px;" id="mic_dictation" onwebkitspeechchange="transcribe(this.value)" type="text" speech="speech" x-webkit-speech="x-webkit-speech"  /> 
    </div>
</div>

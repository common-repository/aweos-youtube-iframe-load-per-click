// const awytLang = [
// 	"Google videos",
// 	"Google Karte laden",
// 	"um die Karte anzuzeigen",
// 	"Die  Karte wurde von Google videos eingebettet.",
// 	"Es gelten die",
// 	"von Google.",
// 	"Datenschutzerklärungen"
// ];

const awyt_policies = "<a target='_blank' href='https://policies.google.com/privacy?hl=de'> " + awytLang[5] + " </a>";
const awyt_anzeigen = "<span title='Video anzeigen' class='awyt-show-video'>" + awytLang[1] + "</span>";
const awyt_style = 'background:linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)),url(' + awytLang[6] + 'youtube.svg);' +
'border: 1px solid grey;';
const awyt_icon = "";

jQuery(function() {
	jQuery('.awyt-video').html(
		"<div class='awyt_video_wrapper' style='" + awyt_style + "'>\
			<h3>" + awytLang[0] + "</h3>\
			<p>"
				+ awyt_anzeigen + " " + "<br>" + awytLang[2] + "<br>" + awytLang[3] + awyt_policies + awytLang[4] +
			"</p>\
		</div>"
	);

	jQuery('span.awyt-show-video').click(function() {
		// `this` is the <a>
		var video = jQuery(this).parent().parent().parent();
		video.replaceWith(function () {
		    return jQuery('<iframe>', {
		        src: video.attr('data-src'),
		        frameborder: video.attr('data-frameborder'),
		        allowfullscreen: video.attr('data-allowfullscreen'),
		        style: video.attr('style'),
		        id: video.attr('id'),
		        class: video.attr('class'),
		        name: video.attr('name'),
		        title: video.attr('tite')
		    });

		});
	})
});

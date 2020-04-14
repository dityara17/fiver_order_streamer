function timeDifference(current, previous) {

 var msPerMinute = 60 * 1000;
 var msPerHour = msPerMinute * 60;
 var msPerDay = msPerHour * 24;
 var msPerMonth = msPerDay * 30;
 var msPerYear = msPerDay * 365;

 var elapsed = current - previous;

 if (elapsed < msPerMinute) {
  return Math.round(elapsed / 1000) + " seconds ago";
} else if (elapsed < msPerHour) {
  return Math.round(elapsed / msPerMinute) + " minutes ago";
} else if (elapsed < msPerDay) {
  return Math.round(elapsed / msPerHour) + " hours ago";
} else if (elapsed < msPerMonth) {
  return Math.round(elapsed / msPerDay) + " days ago";
} else if (elapsed < msPerYear) {
  return Math.round(elapsed / msPerMonth) + " months ago";
} else {
  return Math.round(elapsed / msPerYear) + " years ago";
}
}
function oS(channel_name, channel_platform, url){
  channel_name = channel_name.replace(" ", "_");
  gtag("event", "select_content", {
    "event_category": 'stream_click',
    "event_label": channel_name + "_" + channel_platform
  });

  window.open(url);
}

function es(str){
  str = str.replace("'", "\\'");
  return str;
}

function updateStreams(){
  $.ajax({
    type: "GET",
    url: "tick.php",
    success: function(json){
      var channelListHtml = "";
      for(i=0;i<json.length; i++){
        channelListHtml += '<div class="channel '+json[i]['stream_platform'].toLowerCase()+' '+((json[i]['is_stream_live'] == 1) ? "online" : "") +'" onclick="oS(\''+es(json[i]['channel_name'])+'\', \''+json[i]['stream_platform']+'\', \''+es(json[i]['stream_url'])+'\')">';
        channelListHtml += '    <div class="avatar">';
        channelListHtml += '        <div class="image" style="background-image: url(&quot;'+json[i]['streamer_img_url']+'&quot;);"></div>';
        channelListHtml += '    </div>';
        channelListHtml += '    <div class="details">';
        channelListHtml += '        <div class="name">';
        channelListHtml += '            <div class="channel_name">'+json[i]['channel_name']+'</div>';
        channelListHtml += '            <div class="stream_title">'+json[i]['stream_title']+'</div>';
        channelListHtml += '        </div>';
        channelListHtml += '        <div class="status"><span><time title="'+json[i]['stream_last_live']+'">'+((json[i]['is_stream_live'] == 1) ? json[i]['stream_viewer_count'] : json[i]['last_stream_time_ago'])+'</time></span></div>';
        channelListHtml += '    </div>'
        channelListHtml += '    <div class="dot"></div>';
        channelListHtml += '</div>';
      }

      $("#channel-list").html(channelListHtml);
    }
  });
  
  let headers = {
   'Accept': 'application/vnd.twitchtv.v5+json',
   'Authorization': 'OAuth crqtjmls8z7s94hylo8734v6mydnn8',
   'Client-ID': 'p9262udd6wfjisuzmv4nnfkd5nurab'
 }

 $('.twitch').each(function() {
   fetch('https://api.twitch.tv/helix/users?login=' + $(this).find('.channel_name').text().toLowerCase(), {
     method: 'get',
     headers: headers
   })
   .then(response => (response.ok ? response.json() : console.log('error')))
   .then((json) => {
     $(this).attr('user-id', json.data[0].id);
     fetch('https://api.twitch.tv/kraken/streams/' + json.data[0].id, {
       method: 'get',
       headers: headers
     })
     .then(response => (response.ok ? response.json() : console.log('error')))
     .then((json) => {
       if (json.stream) {
        $(this).find('.stream_title').text(json.stream.channel.status);
        $(this).find('.status').text(json.stream.viewers);
      } else {
        fetch('https://api.twitch.tv/kraken/channels/' + $(this).attr('user-id'), {
          method: 'get',
          headers: headers
        })
        .then(response => (response.ok ? response.json() : console.log('error')))
        .then((json) => {
          $(this).find('.status').text(timeDifference(Date.now(), new Date(json.updated_at).getTime()));
        })

      }


    });
   });

 })
}

updateStreams();

setInterval(function(){
 updateStreams(); 
}, 60000);



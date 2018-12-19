import 'bootstrap/dist/css/bootstrap.min.css';

const urlLength = 70;
const maxElementsInList = 5;

function shortenUrl(url, limit) {
  if(url.length > limit) {
    return url.slice(0,parseInt(limit/2)) + ' ... ' + url.slice(-parseInt(limit/2));
  }
  
  return url;
}

function getLastUrlsAsList (urls, limit) {
  let list = document.createElement('ul')
  list.setAttribute('class', 'list-group list-group-flush')
  
  urls.forEach(function(url) {
    let listItem = document.createElement('li');
    let anchor = document.createElement('a');
    anchor.setAttribute('href', url);
    anchor.setAttribute('target', '_new');
    anchor.textContent = shortenUrl(url, urlLength);
    listItem.appendChild(anchor);
    listItem.setAttribute('class', 'list-group-item list-group-item-primary')
    
    list.appendChild(listItem);
  })
    
  return list;
}

$(document).ready(function () {
  
  let socket = new WebSocket('ws://localhost:8080');
  
  socket.onmessage = function(response) {

  
     $('#currentDomain .content').text(data.currentDomain);
     $('#scraperQueue .content').html(getLastUrlsAsList(data.scraperQueue, maxElementsInList));
     $('#scraperQueue .count').text(data.scraperQueue.length);
     $('#scraperHistory .content').html(getLastUrlsAsList(data.scraperHistory, maxElementsInList));
     $('#scraperHistory .count').text(data.scraperHistory.length);
     $('#domainHistory .content').html(getLastUrlsAsList(data.domainHistory, maxElementsInList));
     $('#domainHistory .count').text(data.domainHistory.length);
     $('#destinations .content').html(getLastUrlsAsList(data.destinations, maxElementsInList));
     $('#destinations .count').text(data.destinations.length);
   } 
});
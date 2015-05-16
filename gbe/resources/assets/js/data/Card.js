
function Card(timestamp, title, body, link, image) {
    this.class = 'Card';
    if (timestamp == null) timestamp = -1;
    this.timestamp = timestamp;
    this.title = title;
    this.body = body;
    this.image = image;
    this.link = link;

    this.getTimestamp = function() {
        return this.timestamp;
    }
};

module.exports = Card;

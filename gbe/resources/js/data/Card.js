
function Card(version, title, body, image, link) {
    this.class = 'Card';
    if (version == null) version = -1;
    this.version = version;
    this.title = title;
    this.body = body;
    this.image = image;
    this.link = link;

    this.getVersion = function() {
        return this.version;
    }
};


module.exports = Card;

var expect = require('expect');
var proxyquire = require('proxyquireify')(require);
var React = require('react/addons');
var TestUtils = React.addons.TestUtils;

describe('SimpleCard', function() {
    beforeEach(function(){
      var test = this

      this.given("card", function(){
        return {title: "foo"}
      })

      this.given("stubs", function(){
        return {
          '../stores/CardStore': {
            getCard: function () { return test.card; }
          }
        };
      })
    })

  it("renders an h1 tag with the title", function(){
    var SimpleCard = proxyquire('../SimpleCard.js', this.stubs);

    var data = { mycard: { ids: [1] } }
    var props = {}
    var storeId = 0

    var card = TestUtils.renderIntoDocument(
      <SimpleCard componentData={ data } componentProps={ props } storeId={ storeId } />
    );

    var h1 = TestUtils.findRenderedDOMComponentWithTag(card, "h1");
    expect(h1.getDOMNode().textContent.trim()).toEqual("foo");
  })
})

/** global describe */

describe('Just for fun', () => {
    it('shoud do something', () => {
        browser.url('/');
        browser.getTitle().should.be.equal('Slim Skeleton');
    });
});

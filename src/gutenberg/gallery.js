/**
 * Gutenberg Gallery Scripts
 *
 * TODO: Refactor to allow use on multiple galleries per page. Currently will
 * only apply gallery styles to first gallery element on the page due to use
 * of .querySelector() rather than .querySelectorAll() - will need to be
 * refactored into a loop.
 *
 * @package     danfoy_2019
 * @subpackage  gutenberg
 * @author      Dan Foy <danfoy.com>
 * @since       1.0.0
 */

// Search for a Gutenberg-generated gallery
const gutenbergGallery = document.querySelector('.wp-block-gallery');

// Only trigger on posts with Gutenberg galleries
if (gutenbergGallery) {

    const gridItemTarget = '.blocks-gallery-item';
    const gridIsCropped = gutenbergGallery.classList.contains('is-cropped');

    // Create a placeholder element for calculating column widths
    const gridItemWidthReference = document.createElement('li');
    gridItemWidthReference.classList.add('blocks-gallery-item', 'reference');
    gutenbergGallery.appendChild(gridItemWidthReference);

    if (gridIsCropped) {
        console.log("Gallery is cropped");
        // Create the Masonry object and set options
        var gutenbergGrid = new Masonry( gutenbergGallery, {
            itemSelector: gridItemTarget,
            columnWidth: gridItemWidthReference,
            stagger: 10
        });
    }


    // Enlarge grid items when they are clicked on
    gutenbergGallery.addEventListener('click', function(event) {

        let gridItem = event.target;

        // Walk through nodelist until we reach the containing grid item
        if (!matchesSelector(gridItem, gridItemTarget)) {
            while (!matchesSelector(gridItem, gridItemTarget)) {
                // Check user hasn't clicked an empty space in the grid
                if (gridItem !== gutenbergGallery) {
                    // Not a match; go up one level
                    gridItem = gridItem.parentElement;
                } else {
                    // Not a grid item; exit
                    return;
                }
            }
        }

        // Toggle a class when clicked, then style via CSS
        gridItem.classList.toggle('is-maximized');

        // Trigger layout reflow
        if (gridIsCropped) {
            gutenbergGrid.layout();
        }
    });
}

<form data-bind="submit: WebBooker.Catalog.loadWithFilters, with: WebBooker.Catalog">
    <input type="text" placeholder="Search Activities" id="menu-search-keywords" data-bind="value: search_params.keywords, valueUpdate: 'afterkeydown'" />
    <input type="submit" hidden="hidden" />
</form>
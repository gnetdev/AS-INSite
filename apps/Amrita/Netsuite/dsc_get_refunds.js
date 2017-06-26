function get_items(input)
{
    var response = {};
    var options = {};
    
    options.search_id = '1416';
    
    options.throttle = 1;
    if (input.throttle) {
        options.throttle = parseInt( input.throttle );
    }
    
    options.offset = 0;
    if (input.offset) {
        options.offset = parseInt( input.offset );
    }
    
    options.limit = 500;
    if (input.limit) {
        options.limit = parseInt( input.limit );
    }

    response.search_id = options.search_id;
    response.totalResults = get_count(input, options);
    response.resultsOffset = options.offset;
    response.resultsLimit = options.limit;
    
    if (options.throttle) {
        response.results = throttledSearch(input, options);
    } else {
        response.results = fullSearch(input, options);
    }

    return response;
}

function get_count(input, options) {
    var savedsearch = nlapiLoadSearch('item', options.search_id);
    var resultset = savedsearch.runSearch();
    
    var length = 0;
    var count = 0, pageSize = 100;
    var currentIndex = 0;
    do{
            count = results.getResults(currentIndex, currentIndex + pageSize).length;
            currentIndex += pageSize;
            length += count;
    }
    while(count == pageSize);
    return length;
}

function throttledSearch(input, options)
{
    var results = [];
    var savedsearch = nlapiLoadSearch('item', options.search_id);
    var resultset = savedsearch.runSearch();
    var offset = options.offset;
    var limit = options.limit;
        
    var resultslice = resultset.getResults( offset, offset+limit );

    for (var rs in resultslice) {
        var result = resultslice[rs];
        var clone = JSON.parse( JSON.stringify( result ) );
        var row = {};
        row.id = clone.id;
        row.recordtype = clone.recordtype;

        var columns = result.getAllColumns();
        var columnLen = columns.length;
        for (i = 0; i < columnLen; i++)
        {
            var column = columns[i];
            var name = column.getName();
            var label = column.getLabel();
            var formula = column.getFormula();
            var functionName = column.getFunction();
            var value = result.getValue(column);

            row[label] = {};
            row[label].value = value;
            row[label].column_name = name;
            if (clone.columns[name] != value) {
                row[label].column_value = clone.columns[name];
            }
            if (formula) {
                row[label].formula = formula;
            }
            if (functionName) {
                row[label].functionName = functionName;
            }
        }

        results.push( row );
    }

    return results;
}

function fullSearch(input)
{
    var results = [];
    var savedsearch = nlapiLoadSearch('item', options.search_id);
    var resultset = savedsearch.runSearch();
    var offset = options.offset;
    var limit = options.limit;
    
    do {
        var resultslice = resultset.getResults( offset, offset + limit );
        
        for (var rs in resultslice) {

            var result = resultslice[rs];
            var clone = JSON.parse( JSON.stringify( result ) );
            var row = {};
            row.id = clone.id;
            row.recordtype = clone.recordtype;

            var columns = result.getAllColumns();
            var columnLen = columns.length;
            for (i = 0; i < columnLen; i++)
            {
                var column = columns[i];
                var name = column.getName();
                var label = column.getLabel();
                var formula = column.getFormula();
                var functionName = column.getFunction();
                var value = result.getValue(column);

                row[label] = {};
                row[label].value = value;
                row[label].column_name = name;
                if (clone.columns[name] != value) {
                    row[label].column_value = clone.columns[name];
                }
                if (formula) {
                    row[label].formula = formula;
                }
                if (functionName) {
                    row[label].functionName = functionName;
                }
            }

            results.push( row );            
            
            offset++;
        }
    } while (resultslice.length >= limit);
    
    return results;
}
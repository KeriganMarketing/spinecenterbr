<?php
/*$stats_url = explode('/',$site);
$statsURL = $stats_url[2];
if($siteID == '70'){ $statsURL = 'abercrombiefoods.com'; }*/
?>
<!--<iframe class="stats" height="550" width="1020" src="http://cpanel.keriganserver.com/cpsess2602253997/awstats.pl?config=<?php //echo $statsURL; ?>"></iframe>
<div class="clear"></div>-->

<!-- Step 1: Create the containing elements. -->

<div id="embed-api-auth-container"></div>
<div id="view-selector-container"></div>

<h2>This Month</h2>
<div style="width:100%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-1-container"></div>
</div>
<div style="width:100%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-7-container"></div>
</div>
<br>
<br>

<!--<div style="width:19%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-8-container"></div>
</div>
<div style="width:19%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-9-container"></div>
</div>
<div style="width:19%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-10-container"></div>
</div>
<div style="width:19%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-11-container"></div>
</div>-->
<div style="width:49%; display:inline-block; vertical-align:text-top;" >
	<div id="chart-5-container"></div>
</div>
<div style="width:49%; display:inline-block; vertical-align:text-top;" >
	<div id="chart-6-container"></div>
</div>


<h2>Year to Date</h2>
<div style="width:100%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-2-container"></div>
</div>
<div style="width:100%; display:inline-block; vertical-align:text-top;" >
    <div id="chart-8-container"></div>
</div>
<br>
<br>
<div style="width:49%; display:inline-block; vertical-align:text-top;" >
	<div id="chart-3-container"></div>
</div>
<div style="width:49%; display:inline-block; vertical-align:text-top;" >
	<div id="chart-4-container"></div>
</div>
    



<!-- Step 2: Load the library. -->

<script>
(function(w,d,s,g,js,fjs){
  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};
  js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
  js.src='https://apis.google.com/js/platform.js';
  fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};
}(window,document,'script'));
</script>


<!-- This demo uses the Chart.js graphing library and Moment.js to do date
     formatting and manipulation. -->
<script src="../js/chart/Chart.min.js"></script>
<script src="../js/moment.min.js"></script>

<!-- Include the ViewSelector2 component script.
<script src="http://requirejs.org/docs/release/2.1.11/minified/require.js"></script> -->
<script src="../js/embed-api/view-selector2.js"></script>

<!-- Include the DateRangeSelector component script. -->
<script src="../js/embed-api/date-range-selector.js"></script>

<!-- Include the ActiveUsers component script. -->
<script src="../js/embed-api/active-users.js"></script>


<script>
gapi.analytics.ready(function() {

  /**
   * Authorize the user immediately if the user has already granted access.
   * If no access has been created, render an authorize button inside the
   * element with the ID "embed-api-auth-container".
   */

  //var CLIENT_ID = '<?php echo $gacode; ?>';
  var CLIENT_ID = '334971613094.apps.googleusercontent.com';

  gapi.analytics.auth.authorize({
    container: 'embed-api-auth-container',
    clientid: CLIENT_ID,
  });

  /**
   * Create a new ViewSelector instance to be rendered inside of an
   * element with the id "view-selector-container".
   */
  var viewSelector = new gapi.analytics.ViewSelector({
    container: 'view-selector-container'
  });

  // Render the view selector to the page.
  viewSelector.execute();


  /**
   * Create a new DataChart instance for pageviews over the past 7 days.
   * It will be rendered inside an element with the id "chart-1-container".
   */

  var dataChart1 = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:date',
      'start-date': '<?php echo date('Y-m'); ?>-01',
      'end-date': 'yesterday'
    },
    chart: {
      container: 'chart-1-container',
      type: 'LINE',
      options: {
        width: '100%'
      }
    }
  });
  
  var dataChart2 = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:date',
      'start-date': '<?php echo date('Y'); ?>-01-01',
      'end-date': 'yesterday'
    },
    chart: {
      container: 'chart-2-container',
      type: 'LINE',
      options: {
        width: '100%'
      }
    }
  });
  
  var dataChart3 = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:deviceCategory',
      'start-date': '<?php echo date('Y'); ?>-01-01',
      'end-date': 'yesterday',
	  'max-results': 6
    },
    chart: {
      container: 'chart-3-container',
      type: 'PIE',
      options: {
        title: 'Device Type',
		width: '100%'
      }
    }
  });
  
  var dataChart4 = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:sourceMedium',
      'start-date': '<?php echo date('Y'); ?>-01-01',
      'end-date': 'yesterday',
	  'max-results': 20,
	  'sort': '-ga:sessions',
    },
    chart: {
      container: 'chart-4-container',
      type: 'TABLE',
      options: {
        title: 'Traffic Sources',
		width: '100%'
      }
    }
  });
  
  var dataChart5 = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:deviceCategory',
      'start-date': '<?php echo date('Y-m'); ?>-01',
      'end-date': 'yesterday',
	  'max-results': 6
    },
    chart: {
      container: 'chart-5-container',
      type: 'PIE',
      options: {
        title: 'Device Type',
		width: '100%'
      }
    }
  });
  
  var dataChart6 = new gapi.analytics.googleCharts.DataChart({
    query: {
      metrics: 'ga:sessions',
      dimensions: 'ga:sourceMedium',
      'start-date': '<?php echo date('Y-m'); ?>-01',
      'end-date': 'yesterday',
	  'max-results': 20,
	  'sort': '-ga:sessions',
    },
    chart: {
      container: 'chart-6-container',
      type: 'TABLE',
      options: {
        title: 'Traffic Sources',
		width: '100%'
      }
    }
  });
  
  var sessionData = new gapi.analytics.googleCharts.DataChart({
    query: {
      'metrics': 'ga:sessions,ga:avgSessionDuration,ga:bounceRate,ga:pageviews,ga:pageviewsPerSession',
      'start-date': '<?php echo date('Y-m'); ?>-01',
      'end-date': 'yesterday',
    },
	chart: {
      container: 'chart-7-container',
      type: 'TABLE',
	  options: {
        width: '100%'
      }
    }
  });
  
  var sessionData2 = new gapi.analytics.googleCharts.DataChart({
    query: {
      'metrics': 'ga:sessions,ga:avgSessionDuration,ga:bounceRate,ga:pageviews,ga:pageviewsPerSession',
      'start-date': '<?php echo date('Y'); ?>01-01',
      'end-date': 'yesterday',
    },
	chart: {
      container: 'chart-8-container',
      type: 'TABLE',
	  options: {
        width: '100%'
      }
    }
  });
	
  

  /**
   * Render both dataCharts on the page whenever a new view is selected.
   */
  viewSelector.on('change', function(ids) {
    dataChart1.set({query: {ids: ids}}).execute();
    dataChart2.set({query: {ids: ids}}).execute();
	dataChart3.set({query: {ids: ids}}).execute();
	dataChart4.set({query: {ids: ids}}).execute();
	dataChart5.set({query: {ids: ids}}).execute();
	dataChart6.set({query: {ids: ids}}).execute();
	sessionData.set({query: {ids: ids}}).execute();
	sessionData2.set({query: {ids: ids}}).execute();
  });

});
</script>
<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/pagination.xslt" />

<!--
//
//  Log template
//
//-->
<xsl:template match="log">
<table class="table table-striped table-condensed table-xs">
<thead>
<tr><th class="timestamp">Timestamp</th><th class="ip_address">IP address</th><th class="user">User</th><th class="user">Switched to</th><th class="event">Event</th></tr>
</thead>
<tbody>
<xsl:for-each select="list/entry">
<tr>
<td><xsl:value-of select="timestamp" /></td>
<td><xsl:value-of select="ip" /></td>
<td><xsl:value-of select="username" /></td>
<td><xsl:value-of select="switch" /></td>
<td><xsl:value-of select="event" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="right">
<xsl:apply-templates select="pagination" />
</div>

<div class="btn-group left">
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1><img src="/images/icons/action.png" class="title_icon" />Action log</h1>
<xsl:apply-templates select="log" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>

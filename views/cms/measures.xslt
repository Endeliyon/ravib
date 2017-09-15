<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/pagination.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-condensed table-stiped table-hover">
<thead>
<tr>
<th>Number</th>
<th>Name</th>
<th class="links">Links</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="measures/measure">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{@id}'">
<td><xsl:value-of select="number" /></td>
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="links" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
<div class="right">
<xsl:apply-templates select="pagination" />
</div>

<div class="btn-group left">
<a href="/{/output/page}/new" class="btn btn-default">New ISO measure</a>
<a href="/cms" class="btn btn-default">Back</a>
<a href="/{/output/page}/categories" class="btn btn-default">Measure categories</a>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="measure/@id">
<input type="hidden" name="id" value="{measure/@id}" />
</xsl:if>

<label for="number">Number</label>
<input type="text" id="number" name="number" value="{measure/number}" class="form-control" />
<label for="name">Name:</label>
<input type="text" id="name" name="name" value="{measure/name}" class="form-control" />
<label for="description">Description:</label>
<textarea id="description" name="description" class="form-control"><xsl:value-of select="measure/description" /></textarea>

<h2>Threats</h2>
<div class="threats">
<xsl:for-each select="threats/threat">
<div class="threat">
<div class="title"><input type="checkbox" name="threat_links[]" value="{@id}"><xsl:if test="@checked='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input><span onClick="javascript:$('#desc_{@id}').slideToggle(0)"><xsl:value-of select="title" /></span></div>
<div class="description" id="desc_{@id}"><xsl:value-of select="description" /></div>
</div>
</xsl:for-each>
</div>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save ISO measure" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="measure/@id">
<input type="submit" name="submit_button" value="Delete ISO measure" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<div class="standard"><xsl:value-of select="standard" /></div>
<img src="/images/icons/measures.png" class="title_icon" /><h1>ISO measure administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>

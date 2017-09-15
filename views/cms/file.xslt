<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Files template
//
//-->
<xsl:template match="files">
<table class="table table-striped table-hover table-condensed">
<thead>
<tr><th></th><th>Filename</th><th>Link</th><th>Filesize</th><th></th></tr>
</thead>
<tbody>
<xsl:if test="back">
<tr><td><img src="/images/directory.png" /></td><td><a href="{back}">&lt;&lt;&lt; one directory up </a></td><td colspan="3"></td></tr>
</xsl:if>
<xsl:for-each select="dir">
<tr>
<td><img src="/images/directory.png" /></td>
<td><a href="{link}">[ <xsl:value-of select="name" /> ]</a></td>
<td></td>
<td></td>
<td><xsl:if test="delete='yes'"><form action="/{/output/page}{../@dir}" method="post">
<input type="hidden" name="filename" value="{name}" />
<input type="submit" name="submit_button" value="delete" class="btn btn-xs btn-primary" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</form></xsl:if></td>
</tr>
</xsl:for-each>
<xsl:for-each select="file">
<tr>
<td><img src="/images/file.png" /></td>
<td><xsl:value-of select="name" /></td>
<td><a href="{link}" target="_blank"><xsl:value-of select="link" /></a></td>
<td><xsl:value-of select="size" /></td>
<td><xsl:if test="delete='yes'"><form action="/{/output/page}{../@dir}" method="post">
<input type="hidden" name="filename" value="{name}" />
<input type="submit" name="submit_button" value="delete" class="btn btn-xs btn-primary" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</form></xsl:if></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<xsl:call-template name="show_messages" />

<div class="row">

<div class="col-sm-6">
<div class="panel panel-default">
<div class="panel-heading">Upload new file</div>
<div class="panel-body">
<form action="/{/output/page}{@dir}" method="post" enctype="multipart/form-data">
<div class="row">
<div class="col-md-7">
<input type="file" name="file" class="form-control" />
</div>
<div class="col-md-5">
<input type="submit" name="submit_button" value="Upload file" class="btn btn-default" />
</div>
</div>
</form>
</div>
</div>
</div>

<div class="col-sm-6">
<div class="panel panel-default">
<div class="panel-heading">Create directory</div>
<div class="panel-body">
<form action="/{/output/page}{@dir}" method="post">
<div class="row">
<div class="col-md-6">
<input type="text" name="create" value="{../create}" class="form-control" />
</div>
<div class="col-md-6">
<input type="submit" name="submit_button" value="Create directory" class="btn btn-default" />
</div>
</div>
</form>
</div>
</div>
</div>

</div>

<div class="btn-group">
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/file.png" class="title_icon" />
<h1>File administration</h1>
<xsl:apply-templates select="files" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>

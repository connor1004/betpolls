/* eslint-disable no-shadow */
import React, { Component, Fragment } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import QueryString from 'qs';
import {
  Page, Spinner
} from '@shopify/polaris';

import Api from '../../../../apis/app';
import View from './View';
import ReplicateAction from './ReplicateAction';
import OptionsHelper from '../../../../helpers/OptionsHelper';
import Futures from './Futures';
import Events from './Events';

class Detail extends Component {
  constructor(props) {
    super(props);
    this.state = {
      page: null,
      polls: [],
      subcategories: [],
      replicates: [],
      replicate_id: '0'
    };
    this.prevSearch = {};

    this.handleUpdate = this.handleUpdate.bind(this);
    this.handleGoBack = this.handleGoBack.bind(this);
    this.handlePollsUpdate = this.handlePollsUpdate.bind(this);
    this.handleReplicate = this.handleReplicate.bind(this);
  }

  async componentDidMount() {
    const {
      params
    } = this.props.match;
    const { body } = await Api.get(`admin/manual/poll-pages/${params.id}/whole`);

    const {
      page, polls
    } = body;

    const { 
      body: subcategories
    } = await Api.get('admin/manual/subcategories', {category_id: page.category_id});

    const {
      body: replicates
    } = await Api.get('admin/manual/poll-pages/replicates', {
      id: page.id,
      category_id: page.category_id,
      is_future: page.is_future
    });

    this.setState({
      page: page,
      polls: polls,
      subcategories: OptionsHelper.getOptions(subcategories, 'id', 'name', {value: '0', label: 'Select...'}),
      replicates: OptionsHelper.getOptions(replicates, 'id', 'name', {value: '0', label: 'Select...'})
    });
  }

  async handleUpdate(values, bags) {
    const {
      params
    } = this.props.match;
    const {
      body, response
    } = await Api.put(`admin/manual/poll-pages/${params.id}`, values);
    const oldIsFuture = this.state.page.is_future;
    switch (response.status) {
      case 422:
        bags.setStatus(body.error);
        break;
      case 200:
        if (oldIsFuture != values.is_future) {
          const {
            body: replicates
          } = await Api.get('admin/manual/poll-pages/replicates', {
            id: this.state.page.id,
            category_id: this.state.page.category_id,
            is_future: values.is_future
          });
          this.setState({
            replicates: OptionsHelper.getOptions(replicates, 'id', 'name', {value: '0', label: 'Select...'}),
            replicate_id: '0',
            polls: [],
            page: body
          });
        }
        else {
          this.setState({
            page: body
          });
        }
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  handleGoBack() {
    this.props.history.goBack();
  }

  handlePollsUpdate(list) {
    this.setState({
      polls: list
    });
  }

  async handleReplicate(replicate_id) {
    const {
      page
    } = this.state;
    const {
      response, body
    } = await Api.put(`admin/manual/poll-pages/${page.id}/replicate/${replicate_id}`);
    switch (response.status) {
      case 422:
        break;
      case 200:
        this.setState({
          replicate_id: '0',
          polls: body
        });
        break;
      default:
        break;
    }
  }

  render() {
    const {
      page, polls, subcategories, replicates, replicate_id
    } = this.state;
    return (
      <Page
        fullWidth
        breadcrumbs={[{ content: 'Polls', onAction: this.handleGoBack }]}
        title={page ? `Manual Poll Detail: ${page.name}` : 'Manual Poll Detail'}
      >
        {
          page ? (
            <Fragment>
              <View
                data={page}
                subcategories={subcategories}
                onUpdate={this.handleUpdate}
              />
              <ReplicateAction
                replicates={replicates}
                onReplicate={this.handleReplicate}
                replicate_id={replicate_id}
              />
              { page.is_future == 1 ?
              <Futures
                page={page}
                list={polls}
                onUpdate={this.handlePollsUpdate}
              />
              :
              <Events
                page={page}
                list={polls}
                onUpdate={this.handlePollsUpdate}
              />
              }
            </Fragment>
          ) : (
            <Spinner />
          )
        }
      </Page>
    );
  }
}


const mapStateToProps = state => ({
  auth: state.common.auth
});

const mapDispatchToProps = () /* dispatch */ => ({
  // logout: bindActionCreators(logout, dispatch)
});

export default withRouter(connect(mapStateToProps, mapDispatchToProps)(Detail));

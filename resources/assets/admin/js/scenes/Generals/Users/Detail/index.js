/* eslint-disable no-shadow */
import React, { Component, Fragment } from 'react';
// import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { withRouter } from 'react-router-dom';
import {
  Page, Spinner
} from '@shopify/polaris';

import Api from '../../../../apis/app';
import View from './View';

class Detail extends Component {
  constructor(props) {
    super(props);
    this.state = {
      user: null
    };
    this.prevSearch = {};

    this.handleUpdate = this.handleUpdate.bind(this);
    this.handleGoBack = this.handleGoBack.bind(this);
  }

  async componentDidMount() {
    const {
      params
    } = this.props.match;
    const { body } = await Api.get(`admin/generals/users/${params.id}`);
    this.setState({
      user: body
    });
  }

  async handleUpdate(values, bags) {
    const {
      params
    } = this.props.match;
    const {
      body, response
    } = await Api.put(`admin/generals/users/${params.id}`, values);
    switch (response.status) {
      case 422:
        bags.setStatus(body.error);
        break;
      case 200:
        break;
      default:
        break;
    }
    await bags.setSubmitting(false);
  }

  handleGoBack() {
    this.props.history.goBack();
  }

  render() {
    const {
      user
    } = this.state;
    return (
      <Page
        fullWidth
        breadcrumbs={[{ content: 'Users', onAction: this.handleGoBack }]}
        title={user ? `User Detail: ${user.firstname} - ${user.lastname}` : 'User Detail'}
      >
        {
          user ? (
            <Fragment>
              <View
                data={user}
                onUpdate={this.handleUpdate}
              />
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
